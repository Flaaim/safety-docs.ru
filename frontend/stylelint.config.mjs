/** @type {import('stylelint').Config} */
const config = {
  extends: ["stylelint-config-standard", "stylelint-order-config-standard"],
  plugins: ["stylelint-order"],
  rules: {
    "at-rule-no-unknown": [
      true,
      {
        "ignoreAtRules": [
          "theme",
          "custom-variant",
          "utility",
          "plugin",
          "apply",
          "variant"
        ]
      }
    ],
    "import-notation": "string"
  }
};

export default config;
