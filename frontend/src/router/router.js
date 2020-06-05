import Vue from 'vue';
import Router from 'vue-router';
import Home from 'src/components/Home/Home';
import SpecialistRegister from 'src/components/SpecialistRegister/SpecialistRegister';
import UserMain from 'src/components/UserMain/UserMain';
import SpecialistMain from 'src/components/SpecialistMain/SpecialistMain';
import Auth from 'src/components/Auth/Auth';
import AdvertContainer from 'src/components/AdvertContainer/AdvertContainer';
import Advert from 'src/components/Advert/Advert';
import CreateAdvert from 'src/components/CreateAdvert/CreateAdvert';
import Reg from 'src/components/Reg/Reg';

Vue.use(Router);

export default new Router({
  mode: 'history',
  routes: [{
    path: '/',
    name: 'Загрузить фото',
    component: Home
  }, {
    path: '/specialist-register',
    name: 'Регистрация специалиста',
    component: SpecialistRegister
  }, {
    path: '/user-main',
    alias: '/dist/user-main',
    name: 'Поиск консультаций',
    component: UserMain
  }, {
    path: '/specialist-main',
    alias: '/dist/specialist-main',
    name: 'Кабинет специалиста',
    component: SpecialistMain
  }, {
    path: '/auth',
    name: 'Авторизация',
    component: Auth
  }, {
    path: '/advert',
    name: 'Объявление',
    component: AdvertContainer,
    children: [{
      path: ':id',
      component: Advert,
      props: {}
    },]
  }, {
    path: '/create-advert',
    name: 'Создать объявление',
    component: CreateAdvert
  }, {
    path: '/reg',
    name: 'Регистрация',
    component: Reg
  }]
});
