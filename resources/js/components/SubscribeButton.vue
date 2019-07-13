<template>
	<button :class="classes" @click="toggle" v-text="subscribed"></button>
</template>

<script>
	export default {
		props: ['active'],

		data() {
			return {
				isActive: this.active,
				endpoint: location.pathname + '/subscriptions'
			}
		},

		computed: {
			classes() {
				return ['btn', 'btn-sm', this.isActive ? 'btn-primary' : 'btn-outline-secondary'];
			},

			subscribed() {
				return this.isActive ? 'Subscribed' : 'Subscribe';
			}
		},

		methods: {
			toggle() {
				return this.isActive ? this.unsubscribe() : this.subscribe();
			},

			subscribe() {
				axios.post(this.endpoint)
				.then(response => {
					flash('Subscribed!');
					this.isActive = true; 
				})
				.catch();
			},

			unsubscribe() {
				axios.delete(this.endpoint)
				.then(response => {
					flash('Unsubscribed');
					this.isActive = false;
				})
				.catch();
			}
		},
	}
</script>