<template>
    <div class="row px-3">
        <!-- quick stats -->
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

        <!-- bills graph -->
        <div class="col-lg-12">
            <div class="card shadow-none">
                <div class="card-header border-0">
                    <span class="card-title text-muted"><i class="fas fa-chart-line ico-tab"></i>Annual Bills Trend</span>
                    <div class="card-tools">
                        <select v-model="yearSelect" class="form-control form-control-sm mr-2" style="width: 90px;" @change="getAnnualBillsStatistics">
                            <option v-for="y in years" :value="y" :key="y">{{ y }}</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <p class="text-muted">Monthly Power Kilowatt-Hour Trend Comparison</p>
                            <div class="mt-3">
                                <Line :data="annualBillsGraphData" :options="lineOptions" style="height: 220px;"/>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <Pie :data="annualBillsPieData" :options="lineOptions"/>
                        </div>
                    </div>
                </div>
                <div class="card-footer"></div>
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
import { Line, Bar, Pie } from 'vue-chartjs'
import { Chart as ChartJS,
        CategoryScale,
        LinearScale,
        PointElement,
        LineElement,
        BarElement,
        Title,
        Tooltip,
        Legend, 
        ArcElement} from 'chart.js'

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement,
    Title,
    Tooltip,
    Legend,
    ArcElement
)

export default {
    components : {
        FlatPickr,
        Swal,
        'pagination' : Bootstrap4Pagination,
        Line,
        Bar,
        Pie
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
            totalStats : {},
            yearSelect : null,
            years : [],
            annualBillsStats : [],
            // bills annual graph
            lineOptions : {
                responsive: true,
                maintainAspectRatio: false
            },
            annualBillsGraphData : {
                labels: [],
                datasets: [
                    {
                        borderColor : '#00968b',
                        backgroundColor : '#afafaf',
                        data: []
                    }
                ]
            },
            // bills annual pie
            annualBillsPieData : {
                labels : [],
                datasets : [
                    {
                        backgroundColor : '#afafaf',
                        data: []
                    }
                ]
            },
            pieColors : [
                '#069e5a', '#069e3e', '#0e9e06', '#5c9e06', '#8f9e06', '#d6d918', '#d99818', '#d96518', '#d92118', '#d91872', '#d918c2', '#af18d9', '#7818d9', '#3313c2', '#365ad1', '#369ed1'
            ],
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
        prepYears() {
            this.yearSelect = moment().format('Y')
            for (let i=0; i<10; i++) {
                var y = parseInt(this.yearSelect)
                this.years.push(y - i)
            }
        },
        getLatestMonthStatistics() {
            axios.get(`${ this.baseURL }/bills/get-latest-month-statistics`)
            .then(response => {
                this.latestMonth = response.data.LatestMonth
                this.totalStats = response.data.TotalStats
            })
            .catch(error => {
                this.toast.fire({
                    icon : 'error',
                    title : 'Error getting statistics!',
                });
                console.log(error.response)
            });
        },
        getAnnualBillsStatistics() {
            axios.get(`${ this.baseURL }/bills/get-bills-annual-stats`, {
                params : {
                    Year : this.yearSelect
                }
            })
            .then(response => {
                this.annualBillsStats = response.data

                this.graphAnnualBillsStatistics()
                this.pieAnnualBillsStatistics()
            })
            .catch(error => {
                this.toast.fire({
                    icon : 'error',
                    title : 'Error getting annual bills stats!',
                });
                console.log(error.response)
            });
        },
        graphAnnualBillsStatistics() {
            // prep data
            var thisYear = []
            var prevYear = []
            for (let i=0; i<12; i++) {
                thisYear.push(this.annualBillsStats.ThisYear[i].TotalKwh)
                prevYear.push(this.annualBillsStats.PreviousYear[i].TotalKwh)
            }

            this.annualBillsGraphData = {
                labels : this.annualBillsStats.Labels,
                datasets : [
                    {
                        label : this.yearSelect,
                        data : thisYear,
                        backgroundColor : 'rgba(196, 189, 93, .2)',
                        borderColor : '#c4bd5d',
                        borderWidth: 2,
                    },
                    {
                        label : parseInt(this.yearSelect)-1,
                        data : prevYear,
                        backgroundColor : 'rgba(23, 191, 135, .2)',
                        borderColor : '#17bf87',
                        borderWidth: 2,
                    },
                ]
            }
        },
        pieAnnualBillsStatistics() {
            var lbls = []
            var data = []
            var cols = []
            var pieRawStats = this.annualBillsStats.PieData
            for(let i=0; i<pieRawStats.length; i++) {
                lbls.push(pieRawStats[i].ConsumerType)
                data.push(parseFloat(pieRawStats[i].Consumption))
                cols.push(this.pieColors[i])
            }

            this.annualBillsPieData = {
                labels : lbls,
                datasets : [
                    {
                        backgroundColor : cols,
                        data : data
                    }
                ]
            }

            console.log(this.annualBillsStats.PieData)
        },
    },
    created() {
        
    },
    mounted() {
        this.prepYears()
        this.getLatestMonthStatistics()
        this.getAnnualBillsStatistics()
    }
}

</script>