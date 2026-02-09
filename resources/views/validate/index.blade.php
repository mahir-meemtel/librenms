@extends('layouts.obzorav1')

@section('title', __('validation.results.validate'))

@push('styles')
<style>
/* Validation result panels - Normal/Light mode styles with maximum specificity */
body .panel-success > .panel-heading,
body .panel-success .panel-heading,
.panel-success > .panel-heading,
.panel-success .panel-heading {
    background-color: rgba(92, 184, 92, 0.2) !important;
    border-bottom: 2px solid #5CB85C !important;
    color: #000000 !important;
}

body .panel-success > .panel-heading *,
body .panel-success .panel-heading *,
.panel-success > .panel-heading *,
.panel-success .panel-heading * {
    color: #5CB85C !important;
    font-weight: 600 !important;
}

/* Warning panel - Black text on orange/yellow background */
body .panel-warning > .panel-heading,
body .panel-warning .panel-heading,
.panel-warning > .panel-heading,
.panel-warning .panel-heading {
    background-color: rgba(255, 183, 51, 0.2) !important;
    border-bottom: 2px solid #FFB733 !important;
    color: #000000 !important;
}

/* Force all text in warning heading to be black */
body .panel-warning > .panel-heading,
body .panel-warning .panel-heading,
.panel-warning > .panel-heading,
.panel-warning .panel-heading,
body .panel-warning > .panel-heading *,
body .panel-warning .panel-heading *,
.panel-warning > .panel-heading *,
.panel-warning .panel-heading * {
    color: #000000 !important;
    font-weight: 600 !important;
}

body .panel-danger > .panel-heading,
body .panel-danger .panel-heading,
.panel-danger > .panel-heading,
.panel-danger .panel-heading {
    background-color: rgba(217, 83, 79, 0.2) !important;
    border-bottom: 2px solid #D9534F !important;
    color: #000000 !important;
}

body .panel-danger > .panel-heading *,
body .panel-danger .panel-heading *,
.panel-danger > .panel-heading *,
.panel-danger .panel-heading * {
    color: #D9534F !important;
    font-weight: 600 !important;
}

body .panel-info > .panel-heading,
body .panel-info .panel-heading,
.panel-info > .panel-heading,
.panel-info .panel-heading {
    background-color: rgba(91, 192, 222, 0.2) !important;
    border-bottom: 2px solid #5BC0DE !important;
    color: #ffffff !important;
}

body .panel-info > .panel-heading *,
body .panel-info .panel-heading *,
.panel-info > .panel-heading *,
.panel-info .panel-heading * {
    color: #5BC0DE !important;
    font-weight: 600 !important;
}

/* Panel body text - Ensure visibility */
/* Success panel body - Black text on green background */
body .panel-success > .panel-body,
body .panel-success .panel-body,
.panel-success > .panel-body,
.panel-success .panel-body {
    background-color: rgba(92, 184, 92, 0.1) !important;
    color: #000000 !important;
    border-left: 3px solid #5CB85C !important;
}

body .panel-success > .panel-body *,
body .panel-success .panel-body *,
.panel-success > .panel-body *,
.panel-success .panel-body * {
    color: #000000 !important;
}

body .panel-success > .panel-body pre,
body .panel-success .panel-body pre,
.panel-success > .panel-body pre,
.panel-success .panel-body pre,
body .panel-success > .panel-body code,
body .panel-success .panel-body code,
.panel-success > .panel-body code,
.panel-success .panel-body code {
    background-color: rgba(0, 0, 0, 0.1) !important;
    color: #000000 !important;
    border: 1px solid rgba(0, 0, 0, 0.2) !important;
}

/* Warning panel body - Black text on orange/yellow background */
body .panel-warning > .panel-body,
body .panel-warning .panel-body,
.panel-warning > .panel-body,
.panel-warning .panel-body {
    background-color: rgba(255, 183, 51, 0.1) !important;
    color: #000000 !important;
    border-left: 3px solid #FFB733 !important;
}

body .panel-warning > .panel-body *,
body .panel-warning .panel-body *,
.panel-warning > .panel-body *,
.panel-warning .panel-body * {
    color: #000000 !important;
}

body .panel-warning > .panel-body pre,
body .panel-warning .panel-body pre,
.panel-warning > .panel-body pre,
.panel-warning .panel-body pre,
body .panel-warning > .panel-body code,
body .panel-warning .panel-body code,
.panel-warning > .panel-body code,
.panel-warning .panel-body code {
    background-color: rgba(0, 0, 0, 0.1) !important;
    color: #000000 !important;
    border: 1px solid rgba(0, 0, 0, 0.2) !important;
}

/* Danger panel body - Black text on red background */
body .panel-danger > .panel-body,
body .panel-danger .panel-body,
.panel-danger > .panel-body,
.panel-danger .panel-body {
    background-color: rgba(217, 83, 79, 0.1) !important;
    color: #000000 !important;
    border-left: 3px solid #D9534F !important;
}

body .panel-danger > .panel-body *,
body .panel-danger .panel-body *,
.panel-danger > .panel-body *,
.panel-danger .panel-body * {
    color: #000000 !important;
}

/* Buttons in danger panel body should have white text */
body .panel-danger > .panel-body .btn,
body .panel-danger .panel-body .btn,
.panel-danger > .panel-body .btn,
.panel-danger .panel-body .btn,
body .panel-danger > .panel-body button,
body .panel-danger .panel-body button,
.panel-danger > .panel-body button,
.panel-danger .panel-body button {
    color: #ffffff !important;
}

body .panel-danger > .panel-body .btn *,
body .panel-danger .panel-body .btn *,
.panel-danger > .panel-body .btn *,
.panel-danger .panel-body .btn *,
body .panel-danger > .panel-body button *,
body .panel-danger .panel-body button *,
.panel-danger > .panel-body button *,
.panel-danger .panel-body button * {
    color: #ffffff !important;
}

body .panel-danger > .panel-body pre,
body .panel-danger .panel-body pre,
.panel-danger > .panel-body pre,
.panel-danger .panel-body pre,
body .panel-danger > .panel-body code,
body .panel-danger .panel-body code,
.panel-danger > .panel-body code,
.panel-danger .panel-body code {
    background-color: rgba(0, 0, 0, 0.1) !important;
    color: #000000 !important;
    border: 1px solid rgba(0, 0, 0, 0.2) !important;
}

/* Info panel body - Black text on blue background */
body .panel-info > .panel-body,
body .panel-info .panel-body,
.panel-info > .panel-body,
.panel-info .panel-body {
    background-color: rgba(91, 192, 222, 0.1) !important;
    color: #000000 !important;
    border-left: 3px solid #5BC0DE !important;
}

body .panel-info > .panel-body *,
body .panel-info .panel-body *,
.panel-info > .panel-body *,
.panel-info .panel-body * {
    color: #000000 !important;
}

body .panel-info > .panel-body pre,
body .panel-info .panel-body pre,
.panel-info > .panel-body pre,
.panel-info .panel-body pre,
body .panel-info > .panel-body code,
body .panel-info .panel-body code,
.panel-info > .panel-body code,
.panel-info .panel-body code {
    background-color: rgba(0, 0, 0, 0.1) !important;
    color: #000000 !important;
    border: 1px solid rgba(0, 0, 0, 0.2) !important;
}

/* Panel heading status indicators - Right side with colors */
/* Ensure panel-title and anchor allow proper positioning */
body .panel-heading .panel-title,
.panel-heading .panel-title {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    width: 100% !important;
}

body .panel-heading .panel-title > a,
.panel-heading .panel-title > a {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    width: 100% !important;
    text-decoration: none !important;
}

/* Position pull-right elements to the right */
body .panel-heading .pull-right.text-success,
.panel-heading .pull-right.text-success,
body .panel-heading .pull-right[class*="success"],
.panel-heading .pull-right[class*="success"],
body .panel-heading .pull-right.text-warning,
.panel-heading .pull-right.text-warning,
body .panel-heading .pull-right[class*="warning"],
.panel-heading .pull-right[class*="warning"],
body .panel-heading .pull-right.text-danger,
.panel-heading .pull-right.text-danger,
body .panel-heading .pull-right[class*="danger"],
.panel-heading .pull-right[class*="danger"] {
    float: right !important;
    margin-left: auto !important;
    margin-left: 10px !important;
    display: inline-block !important;
    order: 2 !important;
}

body .panel-heading .pull-right.text-success,
.panel-heading .pull-right.text-success,
body .panel-heading .pull-right[class*="success"],
.panel-heading .pull-right[class*="success"] {
    color: #5CB85C !important;
    font-weight: 600 !important;
    background-color: rgba(92, 184, 92, 0.15) !important;
    padding: 4px 12px !important;
    border-radius: 4px !important;
    border: 1px solid #5CB85C !important;
}

body .panel-heading .pull-right.text-warning,
.panel-heading .pull-right.text-warning,
body .panel-heading .pull-right[class*="warning"],
.panel-heading .pull-right[class*="warning"] {
    color: #FFB733 !important;
    font-weight: 600 !important;
    background-color: rgba(255, 183, 51, 0.15) !important;
    padding: 4px 12px !important;
    border-radius: 4px !important;
    border: 1px solid #FFB733 !important;
}

body .panel-heading .pull-right.text-danger,
.panel-heading .pull-right.text-danger,
body .panel-heading .pull-right[class*="danger"],
.panel-heading .pull-right[class*="danger"] {
    color: #D9534F !important;
    font-weight: 600 !important;
    background-color: rgba(217, 83, 79, 0.15) !important;
    padding: 4px 12px !important;
    border-radius: 4px !important;
    border: 1px solid #D9534F !important;
}

/* List group item active - Theme-based colors */
/* Success panel - Green */
body .panel-success .list-group-item.active,
body .panel-success .list-group-item.active:focus,
body .panel-success .list-group-item.active:hover,
.panel-success .list-group-item.active,
.panel-success .list-group-item.active:focus,
.panel-success .list-group-item.active:hover {
    z-index: 2 !important;
    color: #ffffff !important;
    background-color: #5CB85C !important;
    border-color: #5CB85C !important;
}

/* Warning panel - Orange */
body .panel-warning .list-group-item.active,
body .panel-warning .list-group-item.active:focus,
body .panel-warning .list-group-item.active:hover,
.panel-warning .list-group-item.active,
.panel-warning .list-group-item.active:focus,
.panel-warning .list-group-item.active:hover {
    z-index: 2 !important;
    color: #000000 !important;
    background-color: #FFB733 !important;
    border-color: #FFB733 !important;
}

/* Danger panel - Red */
body .panel-danger .list-group-item.active,
body .panel-danger .list-group-item.active:focus,
body .panel-danger .list-group-item.active:hover,
.panel-danger .list-group-item.active,
.panel-danger .list-group-item.active:focus,
.panel-danger .list-group-item.active:hover {
    z-index: 2 !important;
    color: #ffffff !important;
    background-color: #D9534F !important;
    border-color: #D9534F !important;
}

/* Info panel - Blue */
body .panel-info .list-group-item.active,
body .panel-info .list-group-item.active:focus,
body .panel-info .list-group-item.active:hover,
.panel-info .list-group-item.active,
.panel-info .list-group-item.active:focus,
.panel-info .list-group-item.active:hover {
    z-index: 2 !important;
    color: #ffffff !important;
    background-color: #5BC0DE !important;
    border-color: #5BC0DE !important;
}
</style>
@endpush

@section('content')
    <div x-data="{results: [], listItems: 10, errorMessage: ''}" x-init="fetch('{{ route('validate.results') }}')
                .then(response => {
                    if (response.ok) {
                        response.json()
                            .then(data => results = data)
                            .catch(error => errorMessage = (error instanceof SyntaxError ? '{{ trans('validation.results.backend_failed') }}' : error))
                    } else {
                        errorMessage = '{{ trans('validation.results.backend_failed') }}';
                        response.text().then(console.log);
                    }
                });">
        <div class="tw:grid tw:place-items-center" style="height: 80vh" x-show="! results.length">
            <h3 x-show="! errorMessage"><i class="fa-solid fa-spinner fa-spin"></i> {{ __('validation.results.validating') }}</h3>
            <div x-show="errorMessage" class="panel panel-danger">
                <div class="panel-heading">
                    <i class="fa-solid fa-exclamation-triangle"></i> {{ __('validation.results.fetch_failed') }}
                </div>
                <div class="panel-body" x-text="errorMessage"></div>
            </div>
        </div>
        <div x-show="results.length" class="tw:mx-10">
            <template x-for="(group, index) in results">
                <div class="panel-group" style="margin-bottom: 5px">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" x-bind:data-target="'#body-' + group.group">
                                    <span x-text="group.name"></span>
                                    <span class="pull-right"
                                          x-bind:class="{'text-success': group.status === 2, 'text-warning': group.status === 1, 'text-danger': group.status === 0}"
                                          x-text="group.statusText"></span>
                                </a>
                            </h4>
                        </div>
                        <div x-bind:id="'body-' + group.group" class="panel-collapse collapse" x-bind:class="{'in': group.status !== 2}">
                            <div class="panel-body">
                                <template x-for="result in group.results">
                                    <div class="panel" x-bind:class="{'panel-info': result.status === 3, 'panel-success': result.status === 2, 'panel-warning': result.status === 1, 'panel-danger': result.status === 0}">
                                        <div class="panel-heading"
                                             x-text="result.statusText + ': ' + result.message"
                                        ></div>
                                        <div class="panel-body" x-show="result.fix.length || result.list.length || result.fixer">
                                            <div x-show="result.fixer" class="tw:mb-2" x-data="fixerData(result.fixer)">
                                                <button class="btn btn-success" x-on:click="runFixer" x-bind:disabled="running" x-show="! fixed">
                                                    <i class="fa-solid" x-bind:class="running ? 'fa-spinner fa-spin' : 'fa-wrench'"></i> {{ __('validation.results.autofix') }}
                                                </button>
                                                <div x-show="fixed">{{ __('validation.results.fixed') }}</div>
                                            </div>
                                            <div x-show="result.fix.length">
                                                {{ __('validation.results.fix') }}: <pre x-text='result.fix.join("\r\n")'>
                                                </pre>
                                            </div>
                                            <div x-show="result.list.length" class="tw:mt-5">
                                                <ul class='list-group' style='margin-bottom: -1px'>
                                                    <li class="list-group-item active" x-text="result.listDescription"></li>
                                                    <template x-for="shortList in result.list.slice(0, listItems)">
                                                        <li class="list-group-item" x-text="shortList"></li>
                                                    </template>
                                                </ul>
                                                <div x-data="{expanded: false}" x-show="result.list.length > listItems">
                                                    <button style="margin-top: 3px" type="button" class="btn btn-default" x-on:click="expanded = ! expanded" x-text="expanded ? '{{ __('validation.results.show_less')}}' : '{{ __('validation.results.show_all')}}'"></button>
                                                    <ul x-show="expanded" class='list-group'>
                                                        <template x-for="longList in result.list.slice(listItems)">
                                                            <li class='list-group-item' x-text="longList"></li>
                                                        </template>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function fixerData(name) {
            return {
                running: false,
                fixed: false,
                fixer: name,
                runFixer() {
                    event.target.disabled = true;
                    fetch('{{ route('validate.fix') }}', {
                        method: 'POST',
                        body: JSON.stringify({fixer: this.fixer}),
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            "X-CSRF-Token": document.querySelector('input[name=_token]').value
                        },
                    }).then(response => {
                        if (response.ok) {
                            this.fixed = true;
                        } else {
                            this.running = false;
                        }
                    }).catch(response => this.running = false);
                }
            }
        }
    </script>
@endpush
