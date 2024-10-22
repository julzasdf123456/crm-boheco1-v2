<template>
    <div class="row px-3">
        <div class="col-lg-12">
            <div class="card shadow-none" style="background: linear-gradient(to right, #17bf87, #c4bd5d);">
                <div class="card-body p-4">
                    <h3 class="no-pads text-white"><strong>{{ moment(latestMonth).format('MMMM YYYY') }}</strong></h3>
                    <p class="no-pads text-white">Billing Month Statistics</p>
                
                    <div class="row mt-4">
                        <div class="col-lg-3">
                            <div class="card shadow-none">
                                <div class="card-body px-4 py-4">
                                    <p class="text-muted no-pads text-sm">Total Power kWH Sales</p>
                                    <div class="d-flex" style="align-items: baseline; column-gap: 8px;">
                                        <h2 class="text-success"><strong>{{ toMoney(parseFloat(totalStats.TotalKwh)) }}</strong></h2>
                                        <h4 class="text-muted no-pads">kWH</h4>
                                    </div>
                                    <p class="text-muted no-pads"><strong>₱ {{ toMoney(parseFloat(totalStats.TotalAmount)) }}</strong> gross amount</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="card shadow-none">
                                <div class="card-body px-4 py-4">
                                    
                                    <h2 class="text-success"><strong>{{ Number(parseFloat(totalStats.TotalBilledAccounts)).toLocaleString("en-US") }}</strong></h2>
                                    <p class="text-muted no-pads">Total Billed Accounts</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3">
                            <div class="card shadow-none">
                                <div class="card-body px-4 py-4">
                                    
                                    <h2 class="text-success"><strong>₱ {{ toMoney(parseFloat(totalStats.TotalDSM)) }}</strong></h2>
                                    <p class="text-muted no-pads">Total DSM</p>
                                </div>
                            </div>
                        </div>
                    </div>
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
import jquery from 'jquery';
import Swal from 'sweetalert2';
import { Line, Bar } from 'vue-chartjs'
import { Chart as ChartJS,
        CategoryScale,
        LinearScale,
        PointElement,
        LineElement,
        BarElement,
        Title,
        Tooltip,
        Legend } from 'chart.js'

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    Title,
    Tooltip,
    Legend
)

export default {
    components : {
        FlatPickr,
        Swal,
        'pagination' : Bootstrap4Pagination,
        Line,
        Bar,
    },
    data() {
        return {
            moment : moment,
            baseURL : window.location.origin + axios.defaults.baseURL,
            imgURL : window.location.origin + axios.defaults.imgURL,
            colorProfile : document.querySelector("meta[name='color-profile']").getAttribute('content'),
            tableInputTextColor : this.isNull(document.querySelector("meta[name='color-profile']").getAttribute('content')) ? 'text-dark' : 'text-white',
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
            latestMonth : null,
            totalStats : {}
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
        getLatestMonthStatistics() {
            axios.get(`${ this.baseURL }/bills/get-latest-month-statistics`)
            .then(response => {
                this.latestMonth = response.data.LatestMonth
                this.totalStats = response.data.TotalStats
                console.log(this.totalStats)
            })
            .catch(error => {
                this.toast.fire({
                    icon : 'error',
                    title : 'Error getting statistics!',
                });
                console.log(error.response)
            });
        }
    },
    created() {
        
    },
    mounted() {
        this.getLatestMonthStatistics()
    }
}

</script>