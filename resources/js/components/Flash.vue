<template>
    <div
        class="flash-alert alert"
        :class="'alert-' + level"
        role="alert"
        v-show="show"
        v-text="body"
    ></div>
</template>

<script>
export default {
    props: ["message"],

    data() {
        return {
            body: null,
            show: false,
            level: "success"
        };
    },

    created() {
        if (this.message) {
            this.flash(this.message);
        }

        window.events.$on("flash", data => this.flash(data));
    },

    methods: {
        flash(data) {
            this.body = data.message;
            this.level = data.level;
            this.show = true;
            this.hide();
        },

        hide() {
            setTimeout(() => {
                this.show = false;
            }, 4000);
        }
    }
};
</script>

<style>
.flash-alert {
    position: fixed;
    right: 30px;
    bottom: 30px;
}
</style>
