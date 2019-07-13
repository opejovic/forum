<template>
	<div>
		<div class="form-group" v-if="signedIn">
			<textarea
				:class="errors.body ? 'form-control is-invalid' : 'form-control'"
				id="body"
				name="body"
				rows="5"
				placeholder="Have something to say?"
				v-model="body"
				@keydown="clear"
			></textarea>

			<span class="invalid-feedback" role="alert" v-if="errors">
				<strong v-text="errors.body[0]"></strong>
			</span>

			<button type="submit" class="btn btn-secondary mt-2" @click="addReply">Post</button>
		</div>

		<p v-else class="text-center">
			Please
			<a href="/login">sign in</a> or
			<a href="/register">register</a> in order to participate in this
			discussion.
		</p>
	</div>
</template>

<script>
	import 'at.js';
	import 'jquery.caret';

	export default {
		props: ["endpoint"],

		data() {
			return {
				body: null,
				errors: false
			};
		},

		mounted() {
			$('#body').atwho({
				at: '@',
				delay: 500,
				callbacks: {
					/*
					If function is given, At.js will invoke it if local filter can not find any data
					@param query [String] matched query
					@param callback [Function] callback to render page.
					*/
					remoteFilter: function(query, callback) {
						$.getJSON('/api/users', { name: query }, function(usernames) {
							callback(usernames);
						});
					}
				}
			});
		},

		methods: {
			addReply() {
				axios
					.post(this.endpoint, {
						body: this.body
					})
					.then(response => {
						this.body = "";
						this.errors = false;
						this.$emit("created", response.data);
						flash("Replied successfuly!");
					})
					.catch(errors => {
						console.log(errors.response.data);
						this.errors = errors.response.data.errors;
					});
			},

			clear() {
				this.errors = false;
			}
		}
	};
</script>
