<template>
	<div>
		<div class="display-4 d-flex align-items-center">
			<img class="mr-2" alt="avatar" width="60px" :src="avatar" />
			<div>{{ user.name }}</div>
		</div>

		<form method="POST" enctype="multipart/form-data" v-if="authorized">
			<image-upload name="avatar" @loaded="onLoad"></image-upload>
		</form>
	</div>
</template>

<script>
	import ImageUpload from "./ImageUpload.vue";

	export default {
		props: ["profileuser"],

		components: { ImageUpload },

		data() {
			return {
				avatar: this.profileuser.avatar_path
			};
		},

		computed: {
			avatarPath() {
				return this.profileuser.avatar_path;
			},

			authorized() {
				return this.profileuser.id == user.id;
			}
		},

		methods: {
			onLoad(data) {
				let avatar = data.file;
				this.avatar = data.src;

				this.persist(avatar);
			},

			persist(avatar) {
				let data = new FormData();
				data.append("avatar", avatar);

				axios
					.post(`/api/users/${this.profileuser.id}/avatar`, data)
					.then(() => flash("Avatar saved"));
			}
		}
	};
</script>