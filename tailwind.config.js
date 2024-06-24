/** @type {import('tailwindcss').Config} */
module.exports = {
	content: ["./web/pages/**/*.php", "./components/views/**/*.php"],
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
			backgroundImage: {
				building: "url(/assets/building.jpg)",
			},
		},
	},
	plugins: [],
};
