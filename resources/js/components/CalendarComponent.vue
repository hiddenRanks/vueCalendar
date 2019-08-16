<template>
    <div>
        <div>
            <h1>Calendar List</h1>
        </div>

        <div class="calendar">
            <div class="calendar-header">
                <a href="#" @click="onClickPrev(currentMonth)"><i class="fas fa-chevron-left"></i></a>
                <div class="day-month">
                    <p class="look-day">
                        {{ currentYear }}년
                    </p>
                    <p class="look-month">
                        {{ currentMonth }}월
                    </p>
                </div>
                <a href="#" @click="onClickNext(currentMonth)"><i class="fas fa-chevron-right"></i></a>
            </div>
            <table>
                <thead>
                    <tr class="thead-week">
                        <td v-for="(weekName, index) in weekNames" v-bind:key="index">
                            {{ weekName }}
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr class="tbody-day" v-for="(row, index) in currentCalendarMatrix" :key="index">
                        <td v-for="(day, index2) in row" :key="index2" @click="todoList(currentYear, currentMonth, day)">
                            <span v-if="isToday(currentYear, currentMonth, day)" class="active">
                                {{ day }}
                            </span>
                            <span v-else>
                                {{ day }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="footer">

        </div>

        <transition name="fade">
            <div class="background"  v-if="listPopup" @click.self="listPopup = false">
                <div class="todo-popup">
                    <div class="popup-title">
                        <a href="#" @click="onClickPrevDay()"><i class="fas fa-chevron-left"></i></a>
                        <p>일정표({{ currentMonth }}/{{ this.sayDay }})</p>
                        <a href="#" @click="onClickNextDay()"><i class="fas fa-chevron-right"></i></a>
                    </div>

                    <transition-group name="fade">
                    <div class="list" v-for="item in msgList" :key="item.id">
                        <div class="calendar-list">
                            <span class="id">{{ item.id }}.</span> <span class="name">{{ item.name }}</span>
                        </div>
                    </div>
                    </transition-group>

                    <button class="closeBtn" @click="listPopup = false">닫기</button>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
    export default {
        name: 'Calendar',
        data() {
            return {
                weekNames: ['Sun', 'Mon', 'Tue','Wed', 'Thu', 'Fri', 'Sat'],
                rootYear: 1904,
                rootDayOfWeekIndex: 4,
                currentYear: new Date().getFullYear(),
                currentMonth: new Date().getMonth() + 1,
                currentDay: new Date().getDate(),
                currentMonthStartWeekIndex: null,
                currentCalendarMatrix: [],
                endOfDay: null,
                msgList:[],
                msgId: 1,
                listPopup: false,
                sayDay: null,
            }
        },
        mounted(){
            this.init();
        },
        methods: {
            showPopup(data){
                this.msgList.push(data);
                // console.log(this.msgList);
            },
            todoList(year, month, day) {
                this.sayDay = day;
                console.log(this.sayDay);
                if(month < 10 || day < 10) {
                    if(month < 10) {
                        month = "0" + month;
                    }
                    if(day < 10) {
                        day = "0" + day;
                    }
                }
                let date = year + "-" + month + "-" +  day;
                let ymd = date + " 00:00:00";

                // console.log(date);
                this.$http.post('/searchDay', { date: ymd }).then( res => {
                    const data = res.data;
                    if(data.success){
                        // console.log(data);
                        for(let i = 0; i < data.result.length; i++) {
                            if(data.result[i].complete == 0) {
                                this.showPopup({id:this.msgId++, name:data.result[i].name});
                            }
                        }
                        this.listPopup = true;
                    }
                });
                this.msgList = [];
            },
            init() {
                this.currentMonthStartWeekIndex = this.getStartWeek(this.currentYear, this.currentMonth);
                this.endOfDay = this.getEndOfDay(this.currentYear, this.currentMonth);
                this.initCalendar();
            },
            initCalendar() {
                this.currentCalendarMatrix = [];
                let day = 1;
                for(let i=0; i<6; i++){
                    let calendarRow = [];
                    for(let j=0; j<7; j++){
                        if(i==0 && j<this.currentMonthStartWeekIndex){
                            calendarRow.push("");
                        }
                        else if(day<=this.endOfDay){
                            //calendarRow.push({id: id++, week: day});
                            calendarRow.push(day);
                            day++;
                        }
                    }
                    this.currentCalendarMatrix.push(calendarRow);
                }
            },
            getEndOfDay(year, month) {
                switch(month){
                    case 1:
                    case 3:
                    case 5:
                    case 7:
                    case 8:
                    case 10:
                    case 12:
                        return 31;
                        break;
                    case 4:
                    case 6:
                    case 9:
                    case 11:
                        return 30;
                        break;
                    case 2:
                        if( (year%4 == 0) && (year%100 != 0) || (year%400 == 0) ){
                            return 29;
                        }
                        else{
                            return 28;
                        }
                        break;
                    default:
                        console.log("unknown month " + month);
                        return 0;
                        break;
                }
            },
            getStartWeek(targetYear, targetMonth) {
                let year = this.rootYear;
                let month = 1;
                let sumOfDay = this.rootDayOfWeekIndex;
                while(true){
                    if(targetYear > year){
                        for(let i=0; i<12; i++){
                            sumOfDay += this.getEndOfDay(year, month+i);
                        }
                        year++;
                    }
                    else if(targetYear == year){
                        if(targetMonth > month){
                            sumOfDay += this.getEndOfDay(year, month);
                            month++;
                        }
                        else if(targetMonth == month){
                            return (sumOfDay)%7;
                        }
                    }
                }
            },
            onClickPrev(month) {
                month--;
                if(month<=0){
                    this.currentMonth = 12;
                    this.currentYear -= 1;
                }
                else{
                    this.currentMonth -= 1;
                }
                this.init();
            },
            onClickNext(month) {
                month++;
                if(month>12){
                    this.currentMonth = 1;
                    this.currentYear += 1;
                }
                else{
                    this.currentMonth += 1;
                }
                this.init();
            },
            onClickPrevDay() {
                let lastDay = this.getEndOfDay(this.currentYear, this.currentMonth);
                this.sayDay -= 1;

                if(this.sayDay <= 0) {
                    this.sayDay = lastDay;
                }

                this.msgList = [];
                this.msgId = 1;
                this.todoList(this.currentYear, this.currentMonth, this.sayDay);
            },
            onClickNextDay() {
                let lastDay = this.getEndOfDay(this.currentYear, this.currentMonth);
                this.sayDay += 1;

                if(this.sayDay > lastDay) {
                    this.sayDay = 1;
                }

                this.msgList = [];
                this.msgId = 1;
                this.todoList(this.currentYear, this.currentMonth, this.sayDay);
            },
            isToday(year, month, day) {
                let date = new Date();
                return year == date.getFullYear() && month == date.getMonth()+1 && day == date.getDate();
            }
        }
    }
</script>

<style scoped>
    .calendar {
        margin: 0 auto;
        width: 90%;
        display: flex;
        flex-direction: column;
        align-items: center;
        box-shadow: 1px 1px 8px rgba(0,0,0,0.25);
    }

    .calendar-header {
        width: 100%;
        height: 150px;
        padding: 0 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #33d879;
        border-radius: 3px 3px 0 0;
        color: #fff;
    }

    .calendar-header > a {
        font-size: 30px;
        color: #fff;
    }

    .day-month {
        width: 300px;
        height: 100px;
        line-height: 1.2;
        font-size: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .look-day {
        font-size: 42px;
        font-weight: bold;
    }

    .look-month {
        font-size: 24px;
    }

    table {
        width: 100%;
    }

    td {
        height: 75px;
    }

    td > span {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
    }

    .thead-week {
        height: 50px;
        text-align: center;
        font-weight: bold;
        font-size: 18px;
        color: #33d879;
    }

    .thead-week > td {
        width: 14.2%;
    }

    .tbody-day {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
    }

    .tbody-day > td > span:hover {
        cursor: pointer;
        background-color: #33d8798f;
    }

    .active {
        color: #fff;
        background-color: #33d879;
    }

    .footer {
        height: 30px;
    }

    .background {
        position: fixed;
        width:100%;
        height:100%;
        left:0;
        top:0;
        background-color: rgba(0,0,0, 0.6);
        display:flex;
        justify-content:center;
        align-items:center;
    }

    .todo-popup {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 1000px;
        height: 500px;
        background-color: #fff;
        border-radius: 3px;
        /*display: none;*/
    }

    .popup-title {
        padding: 0 30px;
        display: flex;
        justify-content: space-between;
        width: 100%;
        height: 70px;
        text-align: center;
        line-height: 70px;
        font-size: 24px;
        font-weight: bold;
        color: #fff;
        background-color: #33d879;
    }

    .popup-title > a {
        font-size: 30px;
        color: #fff;
    }

    .list {
        padding: 5px;
        display: flex;
        justify-content: center;
        width: 100%;
        overflow: auto;
    }

    .calendar-list {
        margin-bottom: 10px;
        width: 80%;
        border-bottom: 1px solid #888;
        display: flex;
        justify-content: center;
    }

    .calendar-list > div {
        display: inline-block;
    }

    .calendar-list > .id {
        width: 50px;
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        color: #888;
    }

    .calendar-list > .name {
        width: 820px;
        font-size: 18px;
        font-weight: 700;
        color: #08d760;
    }

    .closeBtn {
        margin: 10px auto;
        width: 80%;
        height: 50px;
        border: 0;
        border-radius: 3px;
        background-color: #33d879;
        font-size: 18px;
        font-weight: bold;
        color: #fff;
    }
</style>
