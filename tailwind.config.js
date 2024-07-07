/** @type {import('tailwindcss').Config} */
module.exports = {
	content: ["./web/pages/**/*.{php, css}", "./web/**/views/**/*.{php,css}"],
	theme: {
		extend: {
			backgroundColor: {
				primary: "#406191",
			},
			textColor: {
				highlight: "#406191",
			},
			fontFamily: {
				logo: ["Protest Guerrilla"],
				roboto: ["Roboto, sans-serif"],
			},
		},
	},
	plugins: [],
};
