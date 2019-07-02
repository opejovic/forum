<script>
	export default {
		props: ['attributes'],

		data() {
			return {
				editing: false,
				body: this.attributes.body,
			}
		},
		methods: {
			update() {
				axios.patch(`/replies/${this.attributes.id}`, {
					body: this.body,
				})
				.then(response => {
					this.editing = false;
					flash(response.data.message);
				})
				.catch();
			},

			deleteReply() {
				axios.delete(`/replies/${this.attributes.id}`)
				.then(response => {
					$(this.$el).fadeOut(400, () => {
						flash(response.data.message);
					});
				})
				.catch();
			}
		},
	}
</script>