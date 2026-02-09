<?php
namespace ObzoraNMS\Validations\Poller;

use App\Models\Device;
use App\Models\Poller;
use App\Models\PollerCluster;
use ObzoraNMS\DB\Eloquent;
use ObzoraNMS\ValidationResult;

class CheckDispatcherService implements \ObzoraNMS\Interfaces\Validation
{
    /**
     * @inheritDoc
     */
    public function validate(): ValidationResult
    {
        if (PollerCluster::exists()) {
            return $this->checkDispatchService();
        }

        return ValidationResult::ok(trans('validation.validations.poller.CheckDispatcherService.not_detected'));
    }

    /**
     * @inheritDoc
     */
    public function enabled(): bool
    {
        return Eloquent::isConnected() && Device::exists();
    }

    private function checkDispatchService(): ValidationResult
    {
        if (PollerCluster::isActive()->exists()) {
            // check for inactive nodes
            $this_node_id = config('obzora.node_id');
            $inactive = PollerCluster::isInactive()->get()
                ->map(function (PollerCluster $node) use ($this_node_id) {
                    $name = $node->poller_name ?: $node->node_id;

                    // mark this node
                    if ($node->node_id == $this_node_id) {
                        $name .= ' (this node)';
                    }

                    return $name;
                });

            if ($inactive->isNotEmpty()) {
                return ValidationResult::fail(trans('validation.validations.poller.CheckDispatcherService.nodes_down'))
                    ->setList('Inactive Nodes', $inactive->toArray());
            }

            // all ok
            return ValidationResult::ok(trans('validation.validations.poller.CheckDispatcherService.ok'));
        }

        // python wrapper found, just warn
        if (Poller::exists()) {
            $status = Poller::isActive()->exists() ? ValidationResult::SUCCESS : ValidationResult::WARNING;

            return new ValidationResult(trans('validation.validations.poller.CheckDispatcherService.warn'), $status);
        }

        // no python wrapper registered, fail
        return ValidationResult::fail(trans('validation.validations.poller.CheckDispatcherService.fail'));
    }
}
