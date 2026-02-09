

<template>
    <select :multiple="multiple"></select>
</template>

<script>
export default {
    name: "ObzoraSelect",
    props: {
        routeName: {
            type: String,
            required: true
        },
        placeholder: {
            type: String,
            default: '',
        },
        allowClear: {
            type: Boolean,
            default: true
        },
        multiple: {
            type: Boolean,
            default: false
        },
        value: {
            type: [String, Number, Array],
            default: ''
        }
    },
    model: {
        event: 'change',
        prop: 'value'
    },
    data: () => ({
        select2: null
    }),
    methods: {
        checkValue() {
            if (this.value === '' || this.value === []) {
                return true;
            }

            // search for missing options and fetch them
            let values = this.value instanceof Array ? this.value : [this.value];
            if (this.select2.find("option").filter((id, el) => values.includes(el.value)).length < values.length) {
                axios.get(route(this.routeName), {params: {id: values.join(',')}}).then((response) => {
                    response.data.results.forEach((item) => {
                        if (values.find(x => x == item.id) !== undefined) {
                            this.select2.append(new Option(item.text, item.id, false, true));
                        }
                    })

                    this.select2.trigger('change');
                });

                return false;
            }

            return true;
        }
    },
    watch: {
        value(value) {
            if (value instanceof Object && value.hasOwnProperty('id') && value.hasOwnProperty('text')) {
                this.select2.append(new Option(value.text, value.id, true, true))
                    .trigger('change');

                return;
            }

            // check value and if the value doesn't exist, cancel this update to fetch it
           if (! this.checkValue()) {
               return;
           }

            if (value instanceof Array) {
                this.select2.val([...value]);
            } else {
                this.select2.val([value]);
            }

            this.select2.trigger('change');
        }
    },
    computed: {
        settings() {
            return {
                theme: "bootstrap",
                dropdownAutoWidth : true,
                width: "auto",
                allowClear: Boolean(this.allowClear),
                placeholder: this.placeholder,
                multiple: this.multiple,
                ajax: {
                    url: route(this.routeName).toString(),
                    delay: 250,
                    cache: true
                }
            }
        }
    },
    mounted() {
        this.select2 = $(this.$el);

        this.checkValue();

        this.select2.select2(this.settings)
        .on('select2:select select2:unselect', ev => {
            this.$emit('change', this.select2.val());
            this.$emit('select', ev['params']['data']);
        });
    },
    beforeDestroy() {
        this.select2.select2('destroy');
    }
}
</script>

<style scoped>

</style>
