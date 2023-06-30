const withMT = require("@material-tailwind/html/utils/withMT");

const defaultTheme = require("tailwindcss/defaultTheme");

module.exports = withMT({
    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter var", ...defaultTheme.fontFamily.sans],
                Merriweather: ["Merriweather", "serif"],
                // Gentium: ["Gentium", "serif"],
                gentiumPlus: ["Gentium Plus", "serif"],
                // Bokareis: ["Bokareis", "serif"],
            },
        },
    },
    variants: {
        extend: {
            backgroundColor: ["active"],
        },
    },
    content: [
        "./app/**/*.php",
        "./resources/**/*.html",
        "./resources/**/*.js",
        "./resources/**/*.jsx",
        "./resources/**/*.ts",
        "./resources/**/*.tsx",
        "./resources/**/*.php",
        "./resources/**/*.vue",
        "./resources/**/*.twig",
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    ],
    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography"),
    ],
});
