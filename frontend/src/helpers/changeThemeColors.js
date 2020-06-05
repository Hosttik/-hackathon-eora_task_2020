import colors from "vuetify/es5/util/colors";

const changeThemeColors = (vuetifyTheme, colorName) => {
  const theme = colors[colorName];
  vuetifyTheme.primary = theme.lighten1;
  vuetifyTheme.secondary = theme.darken1;
  vuetifyTheme.accent = theme.accent1;
};

export default changeThemeColors;
