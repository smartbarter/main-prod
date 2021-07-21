

<div id="app">
    <div class="page-content header-clear-large" style="background-color: #fafafa;">
        <div class="contact_chat">
            <div class="card">
                <div class="body">
                    <div id="plist" class="people-list">
                        <div id="chat_user">
                            <a href="#" data-menu="menu-instant-3" data-height="220">
                                <div class="chat__contact" @click="changeRoom(0, 'Общий чат')">
                                    <div class="">Общий чат</div>
                                </div>
                            </a>
                            <a href="#" data-menu="menu-instant-3" data-height="220">
                                <div class=" chat__contact" @click="changeRoom(1168, 'Техническая поддержка')">
                                    <div class="">Помощь по системе</div>
                                </div>
                            </a>
                            <div class="content">
                                <div class="content-title  bottom-0">
                                    <h5>Личные сообщения</h5>
                                </div>
                            </div>
                            <a href="#" data-menu="menu-instant-3" data-height="220">
                            <div v-for="user in filteredHistory">

                                    <div class=" chat__contact" @click="changeRoom(user.company_id, user.username)">
                                        <span>{{ user.logo }}</span>
                                        <div class="name">{{ user.username }}</div>
                                        <div class="status">
                                            <!--- <i class="material-icons offline">fiber_manual_record</i>
                                            left 7 mins ago --->
                                        </div>
                                    </div>

                            </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a href="#" id="manual_chat_open" data-menu="menu-instant-3"></a>
    <div id="menu-instant-3"
         class="menu-box"
         data-menu-type="menu-box-right"
         data-menu-height="100px"
         data-menu-width="100%"
         data-menu-effect="menu-over">

        <div class="header header-fixed header-logo-left ">
            <a href="#" class="back-button header-title" style="width: 80%;overflow: hidden;">{{ this.room }}</a>
            <a href="#" class="close-menu header-icon header-icon-1"><i class="fa fa-times-circle"></i></a>
        </div>
        <div class="footer-menu footer-5-icons footer-menu-center-icon">
            <div class="chat-message clearfix">
                <div class="input-style input-style-2 input-required" style="margin-bottom: 0px">
                    <div class="input_chat">
                        <input c minlength="2" @keyup.enter="sendMessage" v-model="message" type="text"
                               class="search" placeholder="Напишите сообщение"
                               style="height: 60px;border-radius: 0px !important;border: none;">
                        <button class="search_btn" @click="sendMessage"><i class="fas fa-angle-right"></i>
                        </button>
                    </div>
                </div>
                <div class="reply" v-if="replyOn.message !== undefined">
                    {{ replyOn.message }}
                    <p @click="replyOn={}">Отмена</p>
                </div>
            </div>

        </div>
        <div class=" header-clear-large" id="chat-conversation"
             style="height: calc(100vh - 60px);overflow: scroll; background-color: #fafafa;">
            <div class="card">
                <div class="chat">
                    <div class="chat-history" >
                        <div v-if="messages.length > 0">
                            <div v-for="message in messages" class="content">
                                <div class="message-data">
                                    <div v-if="isAdmin && room_id == 0">
                                        <button @click="deleteMessage(message.id)">Удалить</button>
                                    </div>
                                    <div>
                                        <user-caption :is-admin="isAdmin" :message="message"></user-caption>
                                    </div>
                                </div>
                                <message-component :message="message"></message-component>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

        </div>

    </div>
</div>



<script>
    $(document).ready(function () {
        if (findGetParameter('im') !== null) {
            var dialog = vex.dialog.alert('Открытие чата...');
            setTimeout(function () {
                vex.close(dialog);
                document.getElementById('manual_chat_open').click();
            }, 1500);
        }
    });

    Vue.component('message-component', {
        template: `
        <div class="message my-message content-box round-medium shadow-small" @mouseenter="replyButton = true" @mouseleave="replyButton = false">

                <span>{{ message.message }}</span>

                <div v-if="!message.delete">
                    <button class="re-mass" v-show="replyButton" @click="replyOnMessage(message)">Ответить</button>
                </div>

            <div v-if="message.reply" class="reply">В ответ на:
                {{ message.reply }}</div>
        </div>
    `,
        props: ['message'],
        data: function () {
            return {
                replyButton: false,
            };
        },
        methods: {
            replyOnMessage: function (data) {
                this.$root.$emit('reply', data);
            },
        },

    });
    Vue.component('user-caption', {
        props: ['is-admin', 'message'],
        template: `
        <div @mouseenter="banButton = true" @mouseleave="banButton = false" style="display: flex; margin-bottom: 10px;">

            <img class="img-chat shadow-tiny" height="45px" width="45px" :src="'https://barter-business.ru/uploads/companys_logo/' + message.logo" />

            <span class="message-data-name font-14 font-600">{{ message.username }} </span>

            <button class="btn"  v-show="banButton" v-if="isAdmin" @click="banUser(message.from_id)"><strong>Заблокировать пользователя</strong></button >

        </div>
    `,

        data() {
            return {
                banButton: false,
            };
        },
        methods: {
            banUser(id) {
                this.$root.$emit('banUser', id);
            },
        },
    });
    Vue.component('company-search', {
        props: ['company'],
        template: `
			<div>
				<a :href="'/company/chat?im='+company.company_id">{{ company.username }}</a>
			</div>
	    `,
    });

    var router = new VueRouter({
        mode: 'history',
        routes: [],
    });
    Vue.use(new VueSocketIO({
        connection: '<?= WEB_SOCKET ?>',
    }));
    var app = new Vue({
        router,
        el: '#app',
        data: {
            message: '',
            messages: [],
            users: [],
            username: '',
            room: 'Общий чат',
            room_id: 0,
            search: '',
            replyOn: {},
            isAdmin: false,
            historySearch: '',
            availableUsers: [],
        },
        sockets: {
            newMessage: function (data) {
                this.messages.push(data);
                this.goToEnd();
            },
            initConnection: function (data) {
                this.username = data.username;
                this.logo = data.logo;
                this.room = data.room_name;
                this.room_id = data.room_id;
                this.users = data.users;
                if (data.history.length > 0) {
                    this.messages = data.history.slice().reverse();
                    this.goToEnd();
                }
                if (data.admin === true) {
                    this.isAdmin = true;
                }
            },
            switchRoom: function (data) {
                this.room = data.room_name;
                if (data.history.length > 0) {
                    this.messages = data.history.slice().reverse();
                    this.goToEnd();
                } else {
                    this.messages = [];
                }

            },
            // userDisconnected: function (data) {
            //     Vue.delete(this.users, data.user_id)
            // },
            // userConnected: function (data) {
            //     Vue.set(this.users, data.id, data)
            // },
            banned: function (data) {
                if (data.status) {
                    swal({
                        type: 'error',
                        text: data.comment,
                    });
                    this.$socket.close();
                }
            },
            messageDelete: function (data) {
                let id;
                this.messages.forEach(function (item, i) {
                    if (item.id === data.message_id) {
                        id = i;
                        return;
                    }
                });
                let temp = _.pick(this.messages[id], ['created_at', 'username']);
                temp.message = 'Сообщение было удалено администратором';
                temp.delete = true;
                Vue.set(this.messages, id, temp);
            },
            getAvailable: function (data) {
                this.availableUsers = data;
            },
        },
        computed: {
            filteredHistory() {
                const search = this.historySearch.toLowerCase().trim();

                if (!search) return this.users;

                return this.users.filter(c => c.username.toLowerCase().indexOf(search) > -1);
            },
        },
        mounted: function () {
            this.$root.$on('reply', function (data) {
                this.replyOn = data;
            });
            this.$root.$on('banUser', function (data) {
                this.$socket.emit('user.ban', data);
            });

            let $im = this.$route.query.im;
            let $init = {
                id: <?= $_SESSION['ses_company_data']['company_id'] ?>
            };

            if ($im !== undefined && fn($im)) {
                $init.im = Number($im);
            }
            this.$socket.emit('login', $init);

            function fn(id) {
                id = Number(id);
                return Number.isInteger(id) &&
                    isFinite(id) &&
                    id >= 0;
            }
        },
        methods: {
            goToEnd: function () {
                this.$nextTick(function () {
                    let c = this.$el.querySelector('#chat-conversation');
                    c.scrollTop = c.scrollHeight;
                });
            },
            sendMessage: function () {
                if (this.replyOn.message !== undefined) {
                    this.$socket.emit('message.reply', {
                        message: this.message,
                        reply: _.pick(this.replyOn, ['message', 'id']),
                    });
                    this.message = '';
                    this.replyOn = {};
                    return;
                }
                this.$socket.emit('message.new', this.message);
                this.message = '';
            },
            changeRoom: function (room) {
                if (history.replaceState) {
                    window.history.replaceState('', '', '?im=' + room);
                    this.room_id = room;
                    this.$socket.emit('room.switch', {
                        room: room,
                    });
                } else {
                    document.location.href = '/?im=' + room;
                }
            },
            deleteMessage: function (id) {
                this.$socket.emit('message.delete', id);
            },
            getAvailableUsers: function () {
                this.$socket.emit('users.available', true);
            },

        },
    });
</script>


