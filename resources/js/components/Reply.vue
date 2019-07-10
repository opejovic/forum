<template>
	<div class="card mb-4">
		<div :id="'reply-'+id" class="card-header d-flex justify-content-between">
			<div>
				<a :href="'/profiles/'+reply.owner.name" v-text="reply.owner.name">
				</a> 
				said {{ reply.created_at }}
			</div>

			<div v-if="signedIn">
				<favorite :reply="reply"></favorite>
			</div>

		</div>

		<div class="card-body">
			<div v-if="editing">
				<div class="form-group">
					<textarea class="form-control" rows="2" v-model="body"></textarea>
				</div>

				<button class="btn btn-sm btn-primary" @click="update">Update</button>
				<button class="btn btn-sm btn-link" @click="clearEdit">Cancel</button>
			</div>

			<div v-else v-text="body">
			</div>
		</div>

		<div class="card-footer d-flex" v-if="canUpdate">
			<button class="btn btn-sm btn-outline-secondary mr-1" @click="editing = true">Edit</button>
			<button class="btn btn-sm btn-outline-danger mr-1" @click="deleteReply">Delete</button>
		</div>

	</div>
</template>

<script>
	import Favorite from './Favorite.vue';

	export default {
		props: ['data'],

		components: { Favorite },

		data() {
			return {
				editing: false,
				body: this.data.body,
				id: this.data.id,
				reply: this.data,
			}
		},

		computed: {
			signedIn() {
				return window.authenticated;
			},

			canUpdate() {
				return this.reply.canUpdate;
			}
		},

		methods: {
			update() {
				axios.patch(`/replies/${this.data.id}`, {
					body: this.body,
				})
				.then(response => {
					this.editing = false;
					flash(response.data.message);
				})
				.catch(error => {
				    flash(error.response.data.message, 'danger');
                });
			},

			deleteReply() {
				axios.delete(`/replies/${this.data.id}`);
				this.$emit('deleted', this.data.id);
				flash('Reply deleted!');
			},

            clearEdit() {
			    this.editing = false;
			    this.body = this.data.body;
            }
		},
	}
</script>
