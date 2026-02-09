

<script>
    export default {
        name: "BaseSetting",
        props: {
            name: {type: String, required: true},
            value: {required: true},
            disabled: Boolean,
            required: Boolean,
            pattern: String,
            "update-status": String,
            options: {}
        }
    }
</script>
