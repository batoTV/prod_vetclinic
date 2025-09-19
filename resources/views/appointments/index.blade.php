@extends('layouts.app')

@section('title', 'Appointments Calendar')

@section('header-actions')
    <a href="/appointments/create" class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700 transition-colors duration-300 flex items-center">
        <i class="fas fa-plus mr-2"></i> Add New Appointment
    </a>
@endsection

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div id="calendar"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                 right: 'dayGridMonth'
            },
            events: '/api/appointments',
             displayEventTime: false,
            // THIS IS THE NEW FUNCTION
            eventClick: function(info) {
                info.jsEvent.preventDefault(); // Don't let the browser navigate
                if (info.event.url) {
                    window.location.href = info.event.url;
                }
            }
        });
        calendar.render();
    });
</script>
@endsection
