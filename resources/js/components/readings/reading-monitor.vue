<template>
    <div class="row">
        <!-- SELECTION -->
        <div class="col-lg-3">
            <span class="text-muted mb-2">Pick Schedule Date</span>
            <div class="card shadow-none mt-1">
                <div class="card-body">
                    <Calendar :is-dark="colorProfile === 'dark-mode'" borderless expanded title-position="left" :attributes="attributes" @dayclick="dateClick"></Calendar>
                </div>
            </div>

            <span class="text-muted">Meter Readers on this Day</span>
            <div class="card shadow-none mt-1" style="height: 50dvh;">
                <div class="card-body table-responsive">
                    <table class="table table-hover table-sm table-borderless">
                        <tbody>
                            <tr class="pointer" v-for="(m, index) in meterReaders" :key="m.ReadBy">
                                <td @click="selectMeterReader(m.ReadBy)" class="v-align">{{ m.ReadBy }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- DATA RESULTS -->
        <div class="col-lg-9">
            <span>Reading Results for <strong>{{ activeMreader }}</strong> on <strong>{{ moment(selectedDate).format("ddd, MMMM DD, YYYY") }}</strong></span>
            <div class="mt-1 card shadow-none" style="height: 88dvh;">
                <div class="card-body table-responsive">
                    <!-- TAB HEADS -->
                    <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="reading-data-tab" data-toggle="pill" href="#reading-data-content" role="tab" aria-controls="reading-data-content" aria-selected="false">reading-data</a>
                        </li>
                    </ul>

                    <!-- TAB CONTENT -->
                    <div class="tab-content" id="custom-tabs-three-tabContent">
                        <div class="tab-pane fade active show" id="reading-data-content" role="tabpanel" aria-labelledby="reading-data-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Account Number</th>
                                            <th>Consumer Name</th>
                                            <th>Consumer Address</th>
                                            <th>Meter Number</th>
                                            <th>Reading Remarks</th>
                                            <th>Present Reading</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(data, index) in readingData" :key="data.AccountNumber">
                                            <td class="v-align">{{ index+ 1}}</td>
                                            <td class="v-align"><strong>{{ data.AccountNumber }}</strong></td>
                                            <td class="v-align">{{ data.ConsumerName }}</td>
                                            <td class="v-align">{{ data.ConsumerAddress }}</td>
                                            <td class="v-align">{{ data.MeterNumber }}</td>
                                            <td class="v-align">{{ data.Remarks }}</td>
                                            <td :class="isNull(data.Present) ? 'bg-danger' : ''" class="v-align">{{ data.Present }}</td>
                                            <td class="v-align text-right">
                                                <button @click="showUpdateMeterNumberModal(data)" title="Update Meter Number" class="btn btn-link-muted btn-sm"><i class="fas fa-tachometer-alt"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- update meter -->
    <div ref="modalUpdateMeter" class="modal fade" id="modal-update-meter" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <span>Update Account Meter Number</span>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Consumer Details</p>
                    <table class="table table-sm table-hover ml-3">
                        <tbody>
                            <tr>
                                <td>Account Number</td>
                                <td><strong>{{ activeReadingData.AccountNumber }}</strong></td>
                            </tr>
                            <tr>
                                <td>Consumer Name</td>
                                <td><strong>{{ activeReadingData.ConsumerName }}</strong></td>
                            </tr>
                            <tr>
                                <td>Consumer Address</td>
                                <td><strong>{{ activeReadingData.ConsumerAddress }}</strong></td>
                            </tr>
                            <tr>
                                <td>Meter Number</td>
                                <td><strong>{{ activeReadingData.MeterNumber }}</strong></td>
                            </tr>
                            <tr>
                                <td>Reading Remarks</td>
                                <td><strong>{{ activeReadingData.Remarks }}</strong></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="divider"></div>

                    <p class="text-muted">Update Consumer Details</p>
                    <table class="table table-sm table-borderless table-hover ml-3">
                        <tbody>
                            <tr>
                                <td class="text-muted v-align">New Meter Number</td>
                                <td class="v-align">
                                    <input v-model="newMeterNumber" type="text" class="form-control form-control-sm" autofocus placeholder="Type or paste new meter number here...">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-primary float-right" @click="saveMeterNumber()"><i class="fas fa-check ico-tab-mini"></i>Update Meter Number</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';
import FlatPickr from 'vue-flatpickr-component';
import { Bootstrap4Pagination } from 'laravel-vue-pagination'
import 'flatpickr/dist/flatpickr.css';
import Swal from 'sweetalert2';
import { Calendar, DatePicker } from 'v-calendar';
import style from 'v-calendar/style.css';

export default {
    components : {
        FlatPickr,
        Swal,
        'pagination' : Bootstrap4Pagination,
        Calendar,
        DatePicker,
        style,
    },
    data() {
        return {
            moment : moment,
            baseURL : window.location.origin + axios.defaults.baseURL,
            imgURL : window.location.origin + axios.defaults.imgURL,
            billPDFs : window.location.origin + axios.defaults.billPDFs,
            colorProfile : document.querySelector("meta[name='color-profile']").getAttribute('content'),
            tableInputTextColor : this.isNull(document.querySelector("meta[name='color-profile']").getAttribute('content')) ? 'text-dark' : 'text-white',
            token : document.querySelector("meta[name='csrf-token']").getAttribute('content'),
            pickerOptions: {
                enableTime: false, // Enable time selection
                dateFormat: 'Y-m-d', // Date format
            },
            toast : Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            }),
            ledger : [],
            rawLedger : {},
            meterInfo : {},
            emailNotifMessage : '',
            readingDates : [],
            meterReaders : [],
            selectedDate : null,
            readingData : [],
            activeMreader : null,
            activeReadingData : {},
            newMeterNumber : null,
            attributes : [
                {
                    key: 'today',
                    highlight: {
                        color: 'green',
                        fillMode: 'solid',
                    },
                    dates: [],
                
                }
            ]
        }
    },
    methods : {
        isNull (value) {
            // Check for null or undefined
            if (value === null || value === undefined) {
                return true;
            }

            // Check for empty string
            if (typeof value === 'string' && value.trim() === '') {
                return true;
            }

            // Check for empty array
            if (Array.isArray(value) && value.length === 0) {
                return true;
            }

            // Check for empty object
            if (typeof value === 'object' && !Array.isArray(value) && Object.keys(value).length === 0) {
                return true;
            }

            // Check for NaN
            if (typeof value === 'number' && isNaN(value)) {
                return true;
            }

            // If none of the above, it's not null, empty, or undefined
            return false;
        },
        toMoney(value) {
            if (this.isNumber(value)) {
                return Number(parseFloat(value).toFixed(2)).toLocaleString("en-US", { maximumFractionDigits: 2, minimumFractionDigits: 2 })
            } else {
                return '-'
            }
        },
        isNumber(value) {
            return typeof value === 'number';
        },        
        round(value) {
            return Math.round((value + Number.EPSILON) * 100) / 100;
        },
        generateRandomString(length) {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let result = '';

            for (let i = 0; i < length; i++) {
                const randomIndex = Math.floor(Math.random() * characters.length);
                result += characters.charAt(randomIndex);
            }

            return result;
        },
        generateUniqueId() {
            return moment().valueOf() + "-" + this.generateRandomString(32);
        },
        getReadingDates() {
            axios.get(`${ this.baseURL }/readings/get-dates-from-upload-history`)
            .then(response => {
                const resData = response.data

                for (let i=0; i<resData.length; i++) {
                    this.readingDates.push(moment(resData[i].ReadingDate).format("YYYY-MM-DD"))
                }

                this.attributes[0].dates = this.readingDates
            })
            .catch(error => {
                Swal.fire({
                    icon : 'error',
                    title : 'Error reading dates!',
                });
                console.log(error.response)
            });
        },
        getMeterReaders(day) {
            axios.get(`${ this.baseURL }/readings/get-meter-readers-by-day`, {
                params : {
                    Day : day,
                }
            })
            .then(response => {
                this.meterReaders = response.data
            })
            .catch(error => {
                Swal.fire({
                    icon : 'error',
                    title : 'Error getting meter readers!',
                });
                console.log(error.response)
            });
        },
        dateClick(day) {
            const dateSelected = moment(day.date).format("YYYY-MM-DD")
            this.selectedDate = dateSelected
            this.getMeterReaders(dateSelected)
        },
        selectMeterReader(mReader) {
            this.activeMreader = mReader
            axios.get(`${ this.baseURL }/readings/get-reading-data-from-day`, {
                params : {
                    Day : this.selectedDate,
                    MeterReader : mReader
                }
            })
            .then(response => {
                this.readingData = response.data
            })
            .catch(error => {
                Swal.fire({
                    icon : 'error',
                    title : 'Error getting reading data!',
                });
                console.log(error.response)
            });
        },
        showUpdateMeterNumberModal(data) {
            this.activeReadingData = data
            let modalElement = this.$refs.modalUpdateMeter
            $(modalElement).modal('show')
        },
        saveMeterNumber() {
            if (this.isNull(this.newMeterNumber)) {
                this.toast.fire({
                    icon : 'info',
                    text : 'Please input new meter number!'
                })
            } else {
                Swal.fire({
                    title : 'Confirm Update',
                    text: "Updating the meter number will not change any preferences on the billing (multiplier, etc.), and will not also change the meter numbers logged in the previous bills. Continue?",
                    showCancelButton: true,
                    confirmButtonText: "Proceed Update",
                    confirmButtonColor : '#e03822'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post(`${ this.baseURL }/readings/update-meter-data`, {
                            _token : this.token,
                            AccountNumber : this.activeReadingData.AccountNumber,
                            NewMeterNumber : this.newMeterNumber
                        })
                        .then(response => {
                            this.toast.fire({
                                icon : 'success',
                                text : 'Meter number updated!'
                            })

                            // hide modal
                            let modalElement = this.$refs.modalUpdateMeter
                            $(modalElement).modal('hide')

                            // update display data
                            this.readingData = this.readingData.map(obj =>
                                obj.AccountNumber === this.activeReadingData.AccountNumber
                                ? { ...obj, MeterNumber : this.newMeterNumber }
                                : obj
                            )

                            // clear new meter field
                            this.newMeterNumber = null
                        })
                        .catch(error => {
                            console.log(error.response)
                            this.toast.fire({
                                icon : 'error',
                                text : 'Error updating meter number!'
                            })
                        })
                    }
                })
            }
        }
    },
    created() {
        
    },
    mounted() {
        this.getReadingDates()
    }
}

</script>