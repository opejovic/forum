<template>
	<div v-if="shouldPaginate">
		<nav aria-label="Page navigation example">
			<ul class="pagination">
				<li :class="prevUrl ? 'page-item' : 'page-item disabled'">
					<a class="page-link" href="#" rel="prev" @click="previousPage">Previous</a>
				</li>
				<li v-for="item in pages" :key="item" class="page-item">
					<a :class="item == page ? 'page-link selected' : 'page-link'" href="#" v-text="item" @click="select(item)"></a>
				</li>
				<li :class="nextUrl ? 'page-item' : 'page-item disabled'">
					<a class="page-link" href="#" rel="next" @click="nextPage">Next</a>
				</li>
			</ul>
		</nav>
	</div>	
</template>

<script>
	export default {
		props: ['dataSet'],
		data() {
			return {
				page: 1,
				prevUrl: false,
				nextUrl: false,
				pages: '',
			}
		},

		watch: {
			dataSet() {
				this.page = this.dataSet.current_page;
				this.prevUrl = this.dataSet.prev_page_url;
				this.nextUrl = this.dataSet.next_page_url;
				this.pages = this.dataSet.last_page;
			},

			page() {
				this.broadcast().updateUrl();
			}
		},

		computed: {
			shouldPaginate() {
				return !! this.prevUrl || !! this.nextUrl;
			},
		},

		methods: {
			next() {
				return this.nextUrl;
			},

			broadcast() {
				this.$emit('updated', this.page);

				return this;
			},

			nextPage() {
				return this.nextUrl ? this.page++ : '';
			},

			previousPage() {
				return this.prevUrl ? this.page-- : '';
			},

			updateUrl() {
				history.pushState(null, null, '?page=' + this.page);
			},

			select(selected) {
				this.page = selected;
			},
		},
	}
</script>

<style>
	.selected, .selected:hover {
		z-index: 1;
	    color: #fff;
	    background-color: #007bff;
	    border-color: #007bff;
	}
</style>