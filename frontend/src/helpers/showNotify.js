import Vue from 'vue';

const showNotify = ({text, type}) => Vue.notify({
  group: 'main',
  title: type === 'error'
         ? 'Error'
         : '',
  text,
  type
});

export default showNotify;
