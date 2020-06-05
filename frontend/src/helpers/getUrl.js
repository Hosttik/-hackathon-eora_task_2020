import config from 'src/config/config';

const getUrl = (path) => `${config.app.host}${path}`;

export default getUrl;
