<template>
    <v-row align="center" justify="center" class="md12">
        <v-col cols="7">
            <v-card max-height="650" min-height="650" >
                <v-card-title class="headline">Видео:</v-card-title>
                <div id="meet"></div>
            </v-card>
        </v-col>
        <v-col cols="4">
            <v-card max-height="600" min-height="600" class="chat">
                <v-card-title class="headline">Чат:</v-card-title>
                <v-list three-line>
                    <template v-for="(message, index) in this.$store.state.messageList">
                        <v-list-item :key="index">
                            <v-list-item-avatar>
                                <v-img :src="getAvatar(message)"></v-img>
                            </v-list-item-avatar>

                            <v-list-item-content>
                                <v-list-item-title>{{getFrom(message)}}</v-list-item-title>
                                <v-list-item-subtitle v-html="message.message"></v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </template>
                </v-list>
            </v-card>
            <v-text-field
                    v-model="chatMsg"
                    placeholder="Введите сообщение"
                    solo
                    v-on:keyup.enter="sendMessage"
            ></v-text-field>
        </v-col>
    </v-row>

</template>

<script>
  import { apiHost } from 'src/api/api.utils';
  import showNotify from 'src/helpers/showNotify';
  import qs from 'qs';
  import { mapMutations } from 'vuex';


  export default {
    name: 'Advert',
    async mounted() {
      const id = this.$route.params.id;
      const domain = 'meet.jit.si';
      const options = {
        roomName: 'TestKirill',
        width: 750,
        height: 500,
        noSsl: false,
        parentNode: document.querySelector('#meet')
      };
      const api = new JitsiMeetExternalAPI(domain, options);
      try {
        const res = await apiHost.post('/adverts-api/get-advert', qs.stringify({id}));
        if (res.is_success) {
          this.advertInfo = res.content.advert;
        } else {

        }
      } catch (e) {
        showNotify({
          text: 'Произошла ошибка',
          type: 'error'
        })

      }
      const userId = this.advertInfo.spec_user_id;
      try {
        const res = await apiHost.post('/chat/get-messages', qs.stringify({from_user_id: userId}));
        if (res.is_success) {
          this.setMessages(res.content.messages);
        } else {
          showNotify({
            text: 'Произошла ошибка',
            type: 'error'
          })
        }
      } catch (e) {
        showNotify({
          text: 'Произошла ошибка',
          type: 'error'
        })

      }
      const self = this;
      this.intervalId = setInterval(async function () {
        try {
          const res = await apiHost.post('/chat/get-messages', qs.stringify({from_user_id: userId}));
          if (res.is_success) {
            self.setMessages(res.content.messages);
          } else {
          }
        } catch (e) {
        }

      }, 5000)

    },
    beforeDestroy() {
      if (this.intervalId) {
        clearInterval(this.intervalId)
      }
    },
    data() {
      return {
        chatMsg: '',
        intervalId: null,
        advertInfo: {}
      }
    },
    methods: {
      ...mapMutations(['setMessages']),
      getFrom: function (message) {
        return message.from_user_id == this.$store.state.userData.user_id
               ? 'Вы:'
               : message.first_name;
      },
      getAvatar: function (message) {
        return message.from_user_id == this.$store.state.userData.user_id
               ? 'https://cdn.vuetifyjs.com/images/lists/2.jpg'
               : 'https://cdn.vuetifyjs.com/images/lists/1.jpg';
      },
      sendMessage: async function () {
        try {
          const res = await apiHost.post('/chat/save-message', qs.stringify({
            to_user_id: this.advertInfo.spec_user_id,
            message: this.chatMsg
          }));
          if (res.is_success) {
            showNotify({
              text: 'Сообщение успешно отправлено',
              type: 'success'
            });
            this.chatMsg = '';
          } else {
            showNotify({
              text: 'Произошла ошибка',
              type: 'error'
            })
          }
        } catch (e) {
          showNotify({
            text: 'Произошла ошибка',
            type: 'error'
          })

        }

      }
    }
  }
</script>

<style scoped>
    .chat {
        max-height: 570px;
        overflow-y: scroll;
    }

    #meet {
        width: 750px;
        margin: 0 auto;
    }

</style>
