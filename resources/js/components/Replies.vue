<template>
	<div>
		<div v-for="(reply, index) in items">
			<reply :key="reply.id" :data="reply" @deleted="remove(index)"></reply>
		</div>

		<paginator :dataSet="dataSet" @updated="fetch"></paginator>

		<new-reply :endpoint="endpoint" @created="add"></new-reply>
	</div>
</template>

<script>
	import Reply from './Reply.vue';
	import NewReply from './NewReply.vue';
	import Paginator from './Paginator.vue';
	import collection from '../mixins/collection.js';

	export default {
		props: [],
		
		components: { Reply, NewReply, Paginator },

		mixins: [collection],

		data() {
			return {
				dataSet: false,
				endpoint: location.pathname + '/replies',
			}
		},

		created() {
			this.fetch();
		},

		methods: {
			url(page) {
				if (! page) {
					let query = location.search.match(/page=(\d+)/);

					page = query ? query[1] : 1;
				}

				return `${location.pathname}/replies?page=${page}`;
			},

			fetch(page) {
				axios.get(this.url(page)).then(this.refresh);
			},

			refresh({data}) {
				this.dataSet = data;
				this.items = data.data;
			}
		},
	}
</script>