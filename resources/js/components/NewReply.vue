<template>
	<div>
		<!-- <form method="POST" action="{{ route('replies.store', [$thread->channel->slug, $thread->id]) }}"> -->
		<div class="form-group">
			<textarea 
				:class="errors.body ? 'form-control is-invalid' : 'form-control'"
				id="body" 
				name="body" 
				rows="5" 
				placeholder="Have something to say?" 
				v-model="body"
				@keydown="clear"></textarea>

				<span class="invalid-feedback" role="alert" v-if="errors.body">
                    <strong v-text="errors.body[0]"></strong>
                </span>
		</div>

		<button type="submit" class="btn btn-secondary" @click="addReply">Post</button>
	<!-- @else
	<p class="text-center">Please <a href="/login">sign in</a> or <a href="/register">register</a> in order to participate in this discussion.</p>
	@endif -->
	</div>	
</template>

<script>
	export default {
		props: ['endpoint'],

		data() {
			return {
				body: null,
				errors: {},
			}
		},

		methods: {
			addReply() {
				axios.post(this.endpoint, {
					body: this.body,
				})
				.then(response => {
					this.body = '';
					this.errors = {};
					flash('Replied successfuly!');
					this.$emit('created', response.data);
				})
				.catch(errors => {
					this.errors = errors.response.data.errors;
				});
			},

			clear() {
				this.errors = {};
			}
		},
	}
</script>