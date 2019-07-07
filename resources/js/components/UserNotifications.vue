<template>
	<li class="nav-item dropdown" v-if="notifications.length">
		<a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
			Notifications
		</a>

		<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
			<a class="dropdown-item" :href="notification.data.path" 
			v-for="notification in notifications" 
			v-text="notification.data.message"
			@click="markAsRead(notification)">
			</a>
		</div>
	</li>	
</template>

<script>
	export default {
		props: ['user'],
		data() {
			return {
				notifications: false,
			}
		},

		created() {
			axios.get(`/profiles/${this.user.name}/notifications`)
			.then(response => this.notifications = response.data);
		},

		methods: {
			markAsRead(notification) {
				axios.delete(`/profiles/${this.user.name}/notifications/${notification.id}`);
			}
		},
	}
</script>