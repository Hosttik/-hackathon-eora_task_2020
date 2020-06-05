export default {
  setIsAuthorized: (state, status) => {
    state.isAuthorized = status;
  },
  setUserData: (state, userData) => {
    state.userData = userData;
  },
  setCurrentAd: (state, adId) => {
    state.currentAdvert = adId;
  },
  setMessages: (state, data) => {
    state.messageList = data;
  },
  changeTheme: (state, newThemeName) => {
    state.theme = newThemeName;
  },
  changeLoaderStatus: (state, {status, message = ''}) => {
    state.loader = {
      status,
      message
    };
  },
};
