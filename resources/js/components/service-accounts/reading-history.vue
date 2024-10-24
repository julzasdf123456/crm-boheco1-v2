<template>
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover">
                    <thead>
                        <th>Billing Month</th>
                        <th>Reading Date</th>
                        <th>Meter Reader</th>
                        <th>kWH</th>
                        <th>Demand kWH</th>
                        <th>Field Findings</th>
                        <th>Remarks</th>
                        <th></th>
                    </thead>
                    <tbody>
                        <tr v-for="reading in readings" :key="reading.BillNumber">
                            <td class="v-align">{{ moment(reading.ServicePeriodEnd).format("MMMM YYYY") }}</td>
                            <td class="v-align">{{ isNull(reading.ReadingDate) ? '' : moment(reading.ReadingDate).format("ddd, MMMM DD, YYYY hh:mm A") }}</td>
                            <td class="v-align">{{ reading.ReadBy }}</td>
                            <td class="v-align text-right">{{ reading.PowerReadings }}</td>
                            <td class="v-align text-right">{{ reading.DemandReadings }}</td>
                            <td class="v-align">{{ reading.FieldFindings }}</td>
                            <td class="v-align">{{ reading.Remarks }}</td>
                            <td class="v-align text-right">
                                <a href="" title="Create a meter-related complain" class="btn btn-link-muted btn-sm"><i class="fas fa-file-import"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <pagination :data="rawReading" :limit="10" @pagination-change-page="getReadings"></pagination>
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
import jquery from 'jquery';
import Swal from 'sweetalert2';
import { renderSlot } from 'vue';

export default {
    components : {
        FlatPickr,
        Swal,
        'pagination' : Bootstrap4Pagination
    },
    data() {
        return {
            moment : moment,
            baseURL : window.location.origin + axios.defaults.baseURL,
            imgURL : window.location.origin + axios.defaults.imgURL,
            colorProfile : document.querySelector("meta[name='color-profile']").getAttribute('content'),
            tableInputTextColor : this.isNull(document.querySelector("meta[name='color-profile']").getAttribute('content')) ? 'text-dark' : 'text-white',
            accountNumber : document.querySelector("meta[name='accountNumber']").getAttribute('content'),
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
            readings : [],
            rawReading : {},
            meterInfo : {},
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
        getReadings(page = 1) {
            axios.get(`${ this.baseURL }/account_masters/get-reading-history`, {
                params: {
                    page : page,
                    AccountNumber : this.accountNumber
                }
            }).then(response => {
                this.readings = response.data.Readings.data
                this.rawReading = response.data.Readings
            })
            .catch(error => {
                Swal.fire({
                    icon : 'error',
                    title : 'Error getting readings!',
                });
                console.log(error.response)
            });
        },
    },
    created() {
        
    },
    mounted() {
        this.getReadings()
    }
}

</script>