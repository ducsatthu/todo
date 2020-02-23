<template>
    <div class="container">
        <h1 class="text-center">Todo list</h1>
        <p>Author: <a href="mailto:ductranxuan.29710@gmail.com">Duc Tran Xuan</a></p>
        <v-btn dark color="primary" href="/add">
            Click here to add new work
        </v-btn>
        <br/>
        <br/>
        <p>
        <span class="red white--text">
            <span class="pl-1">This color is PLANING work</span>
        </span>
            <span class="orange white--text p1">
            <span class="pl-1">This color is DOING work</span>
        </span>
            <span class="cyan white--text p1">
            <span class="pl-1">This color is COMPLETE work</span>
        </span>
        </p>
        <p class="red--text">Click on the worksheet to edit</p>
        <v-row class="fill-height">
            <v-col>
                <v-sheet height="64">
                    <v-toolbar flat color="white">
                        <v-btn outlined class="mr-4" color="grey darken-2" @click="setToday">
                            Today
                        </v-btn>
                        <v-btn fab text small color="grey darken-2" @click="prev">
                            <v-icon small>mdi-chevron-left</v-icon>
                        </v-btn>
                        <v-btn fab text small color="grey darken-2" @click="next">
                            <v-icon small>mdi-chevron-right</v-icon>
                        </v-btn>
                        <v-toolbar-title>{{ title }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-menu bottom right>
                            <template v-slot:activator="{ on }">
                                <v-btn
                                        outlined
                                        color="grey darken-2"
                                        v-on="on"
                                >
                                    <span>{{ typeToLabel[type] }}</span>
                                    <v-icon right>mdi-menu-down</v-icon>
                                </v-btn>
                            </template>
                            <v-list>
                                <v-list-item @click="type = 'day'">
                                    <v-list-item-title>Day</v-list-item-title>
                                </v-list-item>
                                <v-list-item @click="type = 'week'">
                                    <v-list-item-title>Week</v-list-item-title>
                                </v-list-item>
                                <v-list-item @click="type = 'month'">
                                    <v-list-item-title>Month</v-list-item-title>
                                </v-list-item>
                                <v-list-item @click="type = '4day'">
                                    <v-list-item-title>4 days</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </v-toolbar>
                </v-sheet>
                <v-sheet height="600">
                    <v-calendar
                            ref="calendar"
                            v-model="focus"
                            color="primary"
                            :events="events"
                            :event-color="getEventColor"
                            :now="today"
                            :type="type"
                            @click:event="showEvent"
                            @click:more="viewDay"
                            @click:date="viewDay"
                            @change="updateRange"
                    ></v-calendar>
                    <v-menu
                            v-model="selectedOpen"
                            :close-on-content-click="false"
                            :activator="selectedElement"
                            offset-x
                    >
                        <v-card
                                color="grey lighten-4"
                                min-width="350px"
                                flat
                        >
                            <v-toolbar
                                    :color="selectedEvent.color"
                                    dark
                            >
                                <v-btn icon>
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                                <v-toolbar-title>Edit: {{ selectedEvent.name }}</v-toolbar-title>
                            </v-toolbar>
                            <v-card-text>
                                <form method="post" action="/edit">
                                    <v-text-field
                                            v-model="name"
                                            :error-messages="nameErrors"
                                            :counter="50"
                                            label="Work name"
                                            required
                                            @input="$v.name.$touch()"
                                            @blur="$v.name.$touch()"
                                    ></v-text-field>
                                    <v-dialog
                                            ref="dialog"
                                            v-model="modalRange"
                                            :return-value.sync="dates"
                                            persistent
                                            width="290px"
                                    >
                                        <template v-slot:activator="{ on }">
                                            <v-text-field
                                                    v-model="dateRangeText" label="Date range" readonly v-on="on"
                                                    :error-messages="datesErrors"
                                                    @input="$v.dates.$touch()"
                                                    @blur="$v.dates.$touch()"
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker v-model="dates" range>
                                            <v-spacer></v-spacer>
                                            <v-btn text color="primary" @click="modalRange = false">Cancel</v-btn>
                                            <v-btn text color="primary" @click="$refs.dialog.save(dates)">OK</v-btn>
                                        </v-date-picker>
                                    </v-dialog>
                                    <v-select
                                            v-model="statusSelect"
                                            :items="statusItem"
                                            :error-messages="statusErrors"
                                            label="Status"
                                            required
                                            @change="$v.statusSelect.$touch()"
                                            @blur="$v.statusSelect.$touch()"
                                    ></v-select>
                                    <v-btn class="mr-4" color="primary" @click="submit" :disabled="disabled">submit
                                    </v-btn>
                                    <v-btn @click="reset" class="mr-4">Reset</v-btn>
                                    <v-btn @click="deleteItem" color="red">Delete</v-btn>
                                </form>
                            </v-card-text>
                        </v-card>
                    </v-menu>
                </v-sheet>
            </v-col>
        </v-row>
    </div>
</template>

<script>
    import axios from 'axios';
    import {validationMixin} from 'vuelidate';
    import {required, maxLength, date} from 'vuelidate/lib/validators';

    export default {
        name: 'calendar',
        mixins: [validationMixin],
        mounted() {
            this.$refs.calendar.checkChange()
        },
        validations: {
            name: {required, maxLength: maxLength(50)},
            dates: {required},
            statusSelect: {required},
        },
        computed: {
            dateRangeText() {
                return this.dates.join(' ~ ')
            },
            nameErrors() {
                const errors = []
                if (!this.$v.name.$dirty) return errors
                !this.$v.name.maxLength && errors.push('Name must be at most 50 characters long')
                !this.$v.name.required && errors.push('Name is required.')
                return errors
            },
            datesErrors() {
                const errors = []
                if (!this.$v.dates.$dirty) return errors
                !this.$v.dates.required && errors.push('Date range is required.')
                return errors
            },
            statusErrors() {
                const errors = []
                if (!this.$v.statusSelect.$dirty) return errors
                !this.$v.statusSelect.required && errors.push('Status is required.')
                return errors
            },
            title() {
                const {start, end} = this
                if (!start || !end) {
                    return ''
                }

                const startMonth = this.monthFormatter(start)
                const endMonth = this.monthFormatter(end)
                const suffixMonth = startMonth === endMonth ? '' : endMonth

                const startYear = start.year
                const endYear = end.year
                const suffixYear = startYear === endYear ? '' : endYear

                const startDay = start.day + this.nth(start.day)
                const endDay = end.day + this.nth(end.day)

                switch (this.type) {
                    case 'month':
                        return `${startMonth} ${startYear}`
                    case 'week':
                    case '4day':
                        return `${startMonth} ${startDay} ${startYear} - ${suffixMonth} ${endDay} ${suffixYear}`
                    case 'day':
                        return `${startMonth} ${startDay} ${startYear}`
                }
                return ''
            },
            monthFormatter() {
                return this.$refs.calendar.getFormatter({
                    timeZone: 'UTC', month: 'long',
                })
            },
        },
        data: () => ({
            id: null,
            dates: [],
            name: '',
            modalRange: false,
            focus: '',
            type: 'month',
            typeToLabel: {
                month: 'Month',
                week: 'Week',
                day: 'Day',
                '4day': '4 Days',
            },
            start: null,
            end: null,
            selectedEvent: {},
            selectedElement: null,
            selectedOpen: false,
            events: [],
            today: null,
            status: null,
            statusSelect: null,
            statusItem: [
                'Planing',
                'Doing',
                'Complete'
            ],
            disabled: false
        }),
        methods: {
            viewDay({date}) {
                this.focus = date
                this.type = 'day'
            },
            getEventColor(event) {
                return event.color
            },
            setToday() {
                this.focus = this.today
            },
            prev() {
                this.$refs.calendar.prev()
            },
            next() {
                this.$refs.calendar.next()
            },
            showEvent({nativeEvent, event}) {
                const open = () => {
                    this.selectedEvent = Object.assign({}, event);
                    this.selectedElement = nativeEvent.target;

                    this.name = event.name;
                    this.dates = [event.start, event.end];
                    this.id = event.id;
                    this.status = event.status;
                    this.statusSelect = event.statusSelect;

                    setTimeout(() => this.selectedOpen = true, 10)
                }

                if (this.selectedOpen) {
                    this.selectedOpen = false
                    setTimeout(open, 10)
                } else {
                    open()
                }

                nativeEvent.stopPropagation()
            },
            fetchData(start, end) {
                let events = [];
                axios.post('filter', {start: start.date, end: end.date})
                    .then(res => {
                        events = res.data;
                        this.events = events.map(value => {
                            switch (value.status) {
                                case '0':
                                    value.color = 'red';
                                    value.statusSelect = 'Planing';
                                    break;
                                case '1':
                                    value.color = 'orange';
                                    value.statusSelect = 'Doing';
                                    break;
                                case '2':
                                    value.color = 'cyan';
                                    value.statusSelect = 'Complete';
                                    break;
                                default:
                                    value.color = 'red';
                                    value.statusSelect = 'Planing';
                                    break;
                            }

                            return value;
                        })
                    }).catch(() => {
                    this.$toasted.error('Loading error...')
                })
            },
            updateRange({start, end}) {
                this.start = start;
                this.end = end;
                this.fetchData(start, end)
            },
            nth(d) {
                return d > 3 && d < 21
                    ? 'th'
                    : ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'][d % 10]
            },
            submit() {
                this.$v.$touch()
                if (this.$v.$pending || this.$v.$error) return;
                //Call api

                this.disabled = true;
                let start = this.dates[0];
                let end = this.dates[0];
                if (typeof this.dates[1] !== 'undefined') {
                    end = this.dates[1];
                }

                let status = 0;
                switch (this.statusSelect) {
                    case 'Doing':
                        status = 1;
                        break;
                    case 'Complete':
                        status = 2;
                        break;
                    default:
                        status = 0;
                        break;
                }
                axios.put('/update', {
                    id: this.id,
                    name: this.name,
                    start,
                    end,
                    status
                }).then((res) => {
                    this.disabled = false;
                    if (res.data.success) {
                        this.selectedOpen = false;
                        this.fetchData(this.start, this.end);
                    }
                }).catch(err => {
                    this.disabled = false;
                    this.selectedOpen = false;
                    this.$toasted.error('Some thing went wrong!');
                })
            },
            reset() {
                this.$v.$reset()
                this.name = this.selectedEvent.name;
                this.dates = [this.selectedEvent.start, this.selectedEvent.end];
                this.id = this.selectedEvent.id;
                this.status = this.selectedEvent.status;
                this.statusSelect = this.selectedEvent.statusSelect;
            },
            deleteItem() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this work!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.value) {

                        axios.delete('/delete', {
                            data: {
                                id: this.id
                            }
                        }).then((res) => {
                            this.disabled = false;
                            if (res.data.success) {
                                this.selectedOpen = false;
                                this.fetchData(this.start, this.end);
                            }
                        }).catch(err => {
                            this.disabled = false;
                            this.selectedOpen = false;
                            this.$toasted.error('Some thing went wrong!');
                        })
                        Swal.fire(
                            'Deleted!',
                            'Your work file has been deleted.',
                            'success'
                        )
                        // For more information about handling dismissals please visit
                        // https://sweetalert2.github.io/#handling-dismissals
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire(
                            'Cancelled',
                            'Your work is safe :)',
                            'error'
                        )
                    }
                })
            }
        },
    }
</script>
