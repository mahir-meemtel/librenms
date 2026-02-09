

<template>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                {{ $t('Poller Settings') }}
                <span class="pull-right">Advanced <toggle-button v-model="advanced"></toggle-button></span>
            </h3>
        </div>
        <div class="panel-body">
            <vue-tabs direction="vertical" type="pills">
                <v-tab :title="poller.poller_name" v-for="(poller, id) in pollers" :key="id" class="tw:ml-3">
                    <div class="setting-container clearfix"
                         v-for="setting in settings[id]"
                         v-if="!setting.advanced || advanced"
                         :key="setting.name">
                        <obzora-setting
                            prefix="poller.settings"
                            :setting='setting'
                            :id="poller.id"
                        ></obzora-setting>
                    </div>
                </v-tab>
            </vue-tabs>
        </div>
    </div>
</template>

<script>
    export default {
        name: "PollerSettings",
        props: {
            'pollers': Object,
            'settings': Object
        },
        data() {
            return {
                advanced: false
            }
        },
    }
</script>

<style>
    .tab-content {
        width: 100%;
    }
</style>

<style scoped>
    .setting-container {
        margin-bottom: 10px;
    }
</style>
