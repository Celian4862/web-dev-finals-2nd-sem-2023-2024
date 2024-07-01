/** @type {import('tailwindcss').Config} */
module.exports = {
	content: ["./web/pages/**/*.php", "./web/**/views/**/*.php"],
	theme: {
		extend: {
			backgroundColor: {
				primary: "#406191",
			},
			textColor: {
				logo: "#406191",
			},
			fontFamily: {
				logo: ["Protest Guerrilla"],
				roboto: ["Roboto, sans-serif"],
			},
		},
	},
	plugins: [],
};
