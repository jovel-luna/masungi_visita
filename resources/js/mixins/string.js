export default {
	methods: {
		humanize(str) {
			let frags = str.split('_');
			for (let i = 0; i < frags.length; i++) {
				frags[i] = frags[i].charAt(0).toUpperCase() + frags[i].slice(1);
			}
			return frags.join(' ');
		},

		truncate(message) {	
			return message.slice(0, 100) + '. . .';
		}
	},
}