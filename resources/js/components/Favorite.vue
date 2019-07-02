<template>
	<button :class="classes" @click="toggle">
		<i class="material-icons small">
			favorite
		</i>
		<span class="small" v-text="count"></span>
	</button>
</template>

<script>
	export default {
		props: ['reply'],
		
		data() {
			return {
				count: this.reply.favoritesCount,
				active: this.reply.isFavorited,
			}
		},

		computed: {
			classes() {
				return ['btn', this.active ? 'btn-primary' : 'btn-outline-secondary'];
			},

			endpoint() {
				return `/replies/${this.reply.id}/favorites`; 
			}
		},

		methods: {
			toggle() {
				this.active ? this.delete() : this.create();
			},

			create() {
				axios.post(this.endpoint).then().catch();
				this.active = true;
				this.count++;
			},

			delete() {
				axios.delete(this.endpoint).then().catch();
				this.active = false;
				this.count--;
			},
		},
	}
</script>

<style>
	.small { 
		font-size: 15px;
		vertical-align: middle; 
	}
</style>