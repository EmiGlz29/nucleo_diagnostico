<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $cita ? 'Reprogramar cita' : 'Agenda médica' }} | San Miguel</title>
    <link rel="stylesheet" href="{{ asset('management.css') }}">
</head>
<body>
    <nav class="sidebar" aria-label="Navegación principal">
        <div class="sidebar-user">
            Hola, <strong>{{ explode(' ', Auth::user()->name)[0] }}</strong>
        </div>
        <a href="{{ route('menu-principal') }}">Inicio</a>
        <div class="sidebar-item">
            <a href="{{ route('empleados.index') }}">Empleados</a>
            <div class="submenu">
                                @can('gestionar-altas') <a href="{{ route('empleados.create') }}">Agregar empleados</a> @endcan
                <a href="{{ route('empleados.index') }}">Consultas generales</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('doctores.index') }}">Doctores</a>
            <div class="submenu">
                @can('gestionar-altas') <a href="{{ route('doctores.create') }}">Agregar doctores</a> @endcan
                <a href="{{ route('doctores.index') }}">Consultas generales</a>
                <a href="{{ route('citas.create') }}" class="active">Ver agenda</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('citas.create') }}" class="active">Citas</a>
            <div class="submenu">
                <a href="{{ route('citas.create') }}" class="active">Registrar cita</a>
                <a href="{{ route('citas.index') }}">Ver agenda</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('pacientes.index') }}">Pacientes</a>
            <div class="submenu">
                <a href="{{ route('pacientes.create') }}">Agregar paciente</a>
                <a href="{{ route('pacientes.index') }}">Consultas generales</a>
            </div>
        </div>
        <div class="sidebar-item">
            <a href="{{ route('medicamentos.index') }}">Medicamentos</a>
            <div class="submenu">
                                @can('gestionar-altas') <a href="{{ route('medicamentos.create') }}">Agregar medicamento</a> @endcan
                <a href="{{ route('medicamentos.index') }}" class="active">Consultas generales</a>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-logout">Cerrar sesión</button>
        </form>
    </nav>

    <main class="container appointment-page">
        <header class="page-header">
            <div>
                <p class="eyebrow">Núcleo de Diagnóstico</p>
                <h1>{{ $cita ? 'Modificar / reprogramar cita' : 'Agenda médica' }}</h1>
                <p class="page-description">Selecciona doctor, fecha y horario para registrar o reprogramar una cita.</p>
            </div>
            @if ($cita)
                <a class="button button-secondary" href="{{ route('citas.create') }}">Nueva cita</a>
            @endif
        </header>

        <div class="appointment-content">
            @if (session('success'))
                <div class="success-banner" role="status">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert" role="alert">
                    <strong>No se pudo guardar la cita:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="panel appointment-panel" aria-labelledby="doctor-heading">
                <div class="section-heading">
                    <span class="step-number">1</span>
                    <div>
                        <h2 id="doctor-heading">Doctor y calendario</h2>
                        <p>Elige al profesional para consultar sus horarios disponibles.</p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="doctor-select">Doctor</label>
                    <select id="doctor-select" class="appointment-select">
                        <option value="">Selecciona un doctor</option>
                        @foreach ($doctores as $doctor)
                            <option value="{{ $doctor->codigo }}"
                                data-name="{{ $doctor->nombre }}"
                                data-specialty="{{ $doctor->especialidad }}"
                                @selected((string) old('codigodoctor', $cita?->codigodoctor) === (string) $doctor->codigo)>
                                {{ $doctor->nombre }} · {{ $doctor->codigo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="doctor-card" class="doctor-card" hidden>
                    <div class="doctor-avatar" aria-hidden="true">+</div>
                    <div>
                        <p class="doctor-label">Ficha del doctor</p>
                        <h3 id="doctor-name"></h3>
                        <p><strong>Código:</strong> <span id="doctor-code"></span></p>
                        <p><strong>Especialidad:</strong> <span id="doctor-specialty"></span></p>
                        <p><strong>Atención:</strong> lunes a viernes, 09:00 a 20:00 · comida 14:00 a 16:00</p>
                    </div>
                </div>

                <div id="calendar-section" class="calendar-layout" hidden>
                    <section class="calendar-card" aria-label="Calendario mensual">
                        <div class="calendar-heading">
                            <button type="button" class="calendar-nav" id="previous-month" aria-label="Mes anterior">‹</button>
                            <h3 id="calendar-title"></h3>
                            <button type="button" class="calendar-nav" id="next-month" aria-label="Mes siguiente">›</button>
                        </div>
                        <div class="calendar-grid calendar-weekdays" aria-hidden="true">
                            <span>Lun</span><span>Mar</span><span>Mié</span><span>Jue</span><span>Vie</span><span>Sáb</span><span>Dom</span>
                        </div>
                        <div id="calendar-days" class="calendar-grid"></div>
                        <p class="calendar-legend"><span class="legend-dot"></span> horarios libres · gris: fin de semana</p>
                    </section>
                    <section class="agenda-card" aria-labelledby="agenda-heading">
                        <div class="agenda-heading">
                            <div>
                                <p class="doctor-label">Agenda del doctor</p>
                                <h3 id="agenda-heading">Selecciona un día</h3>
                            </div>
                            <div class="view-switch" role="group" aria-label="Vista de agenda">
                                <button type="button" id="day-view" class="view-active">Día</button>
                                <button type="button" id="week-view">Semana</button>
                            </div>
                        </div>
                        <p id="agenda-hint" class="page-description">Selecciona un día entre lunes y viernes para ver sus horarios.</p>
                        <div class="table-wrap agenda-table-wrap">
                            <table class="data-table agenda-table" id="agenda-table">
                                <thead></thead>
                                <tbody><tr><td class="empty-state">La agenda aparecerá aquí.</td></tr></tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </section>

            <section id="patient-panel" class="panel appointment-panel patient-panel" aria-labelledby="patient-heading" hidden>
                <div class="section-heading">
                    <span class="step-number">2</span>
                    <div>
                        <h2 id="patient-heading">Datos del paciente y confirmación</h2>
                        <p>Confirma los datos del horario seleccionado y elige al paciente.</p>
                    </div>
                </div>

                <form action="{{ $cita ? route('citas.update', $cita->codigocita) : route('citas.store') }}" method="POST" id="appointment-form">
                    @csrf
                    @if ($cita)
                        @method('PUT')
                    @endif
                    <input type="hidden" name="codigodoctor" id="form-doctor" value="{{ old('codigodoctor', $cita?->codigodoctor) }}">
                    <input type="hidden" name="fechacita" id="form-date" value="{{ old('fechacita', $cita?->fechacita) }}">
                    <input type="hidden" name="hora" id="form-time" value="{{ old('hora', $cita?->hora ? substr((string) $cita->hora, 0, 5) : '') }}">

                    <div class="appointment-summary">
                        <div><span>Doctor</span><strong id="summary-doctor">—</strong></div>
                        <div><span>Fecha</span><strong id="summary-date">—</strong></div>
                        <div><span>Hora</span><strong id="summary-time">—</strong></div>
                    </div>

                    <div class="form-grid appointment-form-grid">
                        <div class="form-group">
                            <label for="patient-select">Paciente</label>
                            <select id="patient-select" name="codigopaciente" class="appointment-select" required>
                                <option value="">Selecciona un paciente</option>
                                @foreach ($pacientes as $paciente)
                                    <option value="{{ $paciente->codigo }}" @selected((string) old('codigopaciente', $cita?->codigopaciente) === (string) $paciente->codigo)>
                                        {{ $paciente->codigo }} · {{ $paciente->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('codigopaciente') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="appointment-code">Código de cita</label>
                            <input id="appointment-code" name="codigocita" type="number" min="1" max="2147483647" step="1"
                                value="{{ old('codigocita', $cita?->codigocita ?? $nextCode) }}" required>
                            @error('codigocita') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="button-group">
                        <button type="submit" class="button button-primary" id="submit-appointment" disabled
                            @if (! $cita && ! (Auth::user() instanceof \App\Models\EMPLEADO)) hidden @endif>
                            {{ $cita ? 'Confirmar y guardar reprogramación' : 'Confirmar y registrar cita' }}
                        </button>
                        @if (! $cita && ! (Auth::user() instanceof \App\Models\EMPLEADO))
                            <p>Solo los empleados pueden registrar citas.</p>
                        @endif
                        @if ($cita)
                            <a class="button button-secondary" href="{{ route('citas.create') }}">Cancelar</a>
                        @endif
                    </div>
                </form>
            </section>

        </div>
    </main>

    @php
        $initialDate = old('fechacita', $cita?->fechacita);
        $initialTime = old('hora', $cita?->hora ? substr((string) $cita->hora, 0, 5) : '');
    @endphp
    <script>
        (() => {
            const doctors = document.getElementById('doctor-select');
            const doctorCard = document.getElementById('doctor-card');
            const calendarSection = document.getElementById('calendar-section');
            const daysElement = document.getElementById('calendar-days');
            const monthTitle = document.getElementById('calendar-title');
            const table = document.getElementById('agenda-table');
            const patientPanel = document.getElementById('patient-panel');
            const submitButton = document.getElementById('submit-appointment');
            const endpoint = @json(route('citas.agenda'));
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const baseCitasUrl = @json(url('/citas'));
            const minAppointmentDate = @json($minAppointmentDate);
            const oldDate = @json($initialDate);
            const oldTime = @json($initialTime);
            let visibleMonth = oldDate ? new Date(`${oldDate}T12:00:00`) : new Date();
            let selectedDate = '';
            let selectedTime = '';
            let currentBookings = [];
            let agendaMode = 'day';

            const isoDate = (date) => `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
            const prettyDate = (value) => new Date(`${value}T12:00:00`).toLocaleDateString('es-MX', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            const titleDate = (value) => new Date(`${value}T12:00:00`).toLocaleDateString('es-MX', { day: '2-digit', month: 'short' }).toUpperCase();
            const chosenDoctor = () => doctors.options[doctors.selectedIndex];
            const today = isoDate(new Date());
            const isBeforeMinimum = (value) => value < minAppointmentDate;
            const appointmentsByDate = () => currentBookings.reduce((grouped, appointment) => {
                const date = String(appointment.fechacita).slice(0, 10);
                grouped[date] = (grouped[date] || 0) + 1;
                return grouped;
            }, {});

            async function loadBookings(start, end) {
                const params = new URLSearchParams({
                    codigodoctor: doctors.value,
                    fecha: start,
                    hasta: end,
                });
                const response = await fetch(`${endpoint}?${params}`, {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'No se pudo consultar la agenda.');
                currentBookings = result.citas;
                return currentBookings;
            }

            function chooseSlot(date, time) {
                if (isBeforeMinimum(date)) return;
                selectedDate = date;
                selectedTime = time;
                document.getElementById('form-doctor').value = doctors.value;
                document.getElementById('form-date').value = date;
                document.getElementById('form-time').value = time;
                document.getElementById('summary-doctor').textContent = chosenDoctor().dataset.name;
                document.getElementById('summary-date').textContent = prettyDate(date);
                document.getElementById('summary-time').textContent = `${time} - ${String(Number(time.slice(0, 2)) + 1).padStart(2, '0')}:00`;
                patientPanel.hidden = false;
                submitButton.disabled = false;
                renderCalendar();
                renderAgenda();
            }

            function renderCalendar() {
                const year = visibleMonth.getFullYear();
                const month = visibleMonth.getMonth();
                monthTitle.textContent = visibleMonth.toLocaleDateString('es-MX', { month: 'long', year: 'numeric' });
                daysElement.replaceChildren();
                const startOffset = (new Date(year, month, 1).getDay() + 6) % 7;
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const booked = appointmentsByDate();

                for (let blank = 0; blank < startOffset; blank++) {
                    const spacer = document.createElement('span');
                    spacer.className = 'calendar-spacer';
                    daysElement.append(spacer);
                }

                for (let day = 1; day <= daysInMonth; day++) {
                    const date = isoDate(new Date(year, month, day, 12));
                    const weekend = new Date(year, month, day).getDay() % 6 === 0;
                    const occupied = booked[date] || 0;
                    const free = Math.max(0, 9 - occupied);
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'calendar-day';
                    button.textContent = String(day);
                    button.disabled = weekend;
                    button.setAttribute('aria-label', `${date}, ${weekend ? 'sin atención' : `${free} horarios libres`}`);
                    if (weekend) button.classList.add('calendar-weekend');
                    else button.classList.add(free === 0 ? 'calendar-full' : 'calendar-available');
                    if (date === selectedDate) button.classList.add('calendar-selected');
                    if (date === today) button.classList.add('calendar-today');
                    if (!weekend) {
                        const count = document.createElement('small');
                        count.textContent = String(free);
                        button.append(count);
                        button.addEventListener('click', () => {
                            selectedDate = date;
                            selectedTime = '';
                            patientPanel.hidden = true;
                            renderCalendar();
                            renderAgenda();
                        });
                    }
                    daysElement.append(button);
                }
            }

            function addCell(row, content, className = '') {
                const cell = document.createElement('td');
                cell.textContent = content;
                if (className) cell.className = className;
                row.append(cell);
                return cell;
            }
 //La aguregué para que se pueda eliminar la cita desde la agenda
            function createActionButtons(code) {
            const wrapper = document.createElement('div');
            wrapper.style.display = 'flex';
            wrapper.style.gap = '6px';

            // Botón Reprogramar / Editar
            const editLink = document.createElement('a');
            editLink.href = `${baseCitasUrl}/${encodeURIComponent(code)}/edit`;
            editLink.className = 'button button-secondary';
            editLink.textContent = 'Editar';

            // Botón Eliminar (usa un formulario POST con _method=DELETE)
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `${baseCitasUrl}/${encodeURIComponent(code)}`;
            form.onsubmit = () => confirm(`¿Seguro que deseas eliminar la cita ${code}?`);
            form.innerHTML = `
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="button">Eliminar</button>
            `;

            wrapper.append(editLink, form);
            return wrapper;
        }

            function createSlotCell(date, time, appointments) {
                const occupied = appointments.find((appointment) => String(appointment.hora).slice(0, 5) === time);
                const cell = document.createElement('td');
                if (time === '14:00' || time === '15:00') {
                    cell.textContent = 'HORARIO DE COMIDA';
                    cell.className = 'slot-break';
                } else if (occupied) {
                    cell.className = 'slot-occupied';
                    const name = document.createElement('strong');
                    name.textContent = occupied.paciente;
                    const code = document.createElement('small');
                    code.textContent = `Cita ${occupied.codigocita}`;
                    cell.append(name, code, createActionButtons(occupied.codigocita));
                } else {
                    cell.className = 'slot-free';
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'slot-select';
                    button.textContent = selectedDate === date && selectedTime === time ? '✓ Seleccionado' : 'Seleccionar';
                    button.disabled = isBeforeMinimum(date);
                    button.addEventListener('click', () => chooseSlot(date, time));
                    cell.append(button);
                }
                return cell;
            }

            function renderDay(date, bookings) {
            const head = table.querySelector('thead');
            const body = table.querySelector('tbody');
            head.replaceChildren();
            body.replaceChildren();

            const header = document.createElement('tr');
            ['Horario', 'Estado', 'Paciente / cita', 'Acción'].forEach((label) => {
                const th = document.createElement('th');
                th.scope = 'col';
                th.textContent = label;
                header.append(th);
            });
            head.append(header);

            const entries = bookings.filter((appointment) => String(appointment.fechacita).slice(0, 10) === date);

            for (let hour = 9; hour <= 19; hour++) {
                const time = `${String(hour).padStart(2, '0')}:00`;
                const row = document.createElement('tr');
                addCell(row, `${time} - ${String(hour + 1).padStart(2, '0')}:00`);

                const status = document.createElement('td');
                const busy = entries.some((appointment) => String(appointment.hora).slice(0, 5) === time);
                status.textContent = hour === 14 || hour === 15 ? 'COMIDA' : busy ? 'OCUPADO' : 'LIBRE';
                status.className = hour === 14 || hour === 15 ? 'status-break' : busy ? 'status-busy' : 'status-free';
                row.append(status);

                const appointmentCell = document.createElement('td');
                const appointment = entries.find((item) => String(item.hora).slice(0, 5) === time);
                appointmentCell.textContent = appointment
                    ? `${appointment.paciente} · ${appointment.codigocita}`
                    : hour === 14 || hour === 15
                        ? 'HORARIO DE COMIDA'
                        : '—';
                row.append(appointmentCell);

                let action;
                if (hour === 14 || hour === 15) {
                    action = addCell(row, '—');
                } else if (appointment) {
                    action = document.createElement('td');
                    action.append(createActionButtons(appointment.codigocita));
                    row.append(action);
                } else {
                    action = createSlotCell(date, time, entries);
                    row.append(action);
                }

                if (action.classList.contains('slot-free') && selectedDate === date && selectedTime === time) {
                    row.classList.add('slot-row-selected');
                }

                body.append(row);
            }
        }

            async function renderWeek(date) {
                const selected = new Date(`${date}T12:00:00`);
                const monday = new Date(selected);
                monday.setDate(selected.getDate() - ((selected.getDay() + 6) % 7));
                const dates = Array.from({ length: 5 }, (_, index) => {
                    const day = new Date(monday);
                    day.setDate(monday.getDate() + index);
                    return isoDate(day);
                });
                const bookings = await loadBookings(dates[0], dates[4]);
                const head = table.querySelector('thead');
                const body = table.querySelector('tbody');
                head.replaceChildren();
                body.replaceChildren();
                const header = document.createElement('tr');
                ['Horario', ...dates.map(titleDate)].forEach((label) => {
                    const th = document.createElement('th');
                    th.scope = 'col';
                    th.textContent = label;
                    header.append(th);
                });
                head.append(header);

                for (let hour = 9; hour <= 19; hour++) {
                    const time = `${String(hour).padStart(2, '0')}:00`;
                    const row = document.createElement('tr');
                    addCell(row, `${time} - ${String(hour + 1).padStart(2, '0')}:00`);
                    dates.forEach((day) => {
                        const appointments = bookings.filter((appointment) => String(appointment.fechacita).slice(0, 10) === day);
                        row.append(createSlotCell(day, time, appointments));
                    });
                    body.append(row);
                }
                document.getElementById('agenda-heading').textContent = `Semana del ${titleDate(dates[0])} al ${titleDate(dates[4])}`;
            }

            async function renderAgenda() {
                if (!selectedDate) return;
                document.getElementById('agenda-heading').textContent = prettyDate(selectedDate);
                document.getElementById('agenda-hint').textContent = isBeforeMinimum(selectedDate)
                    ? 'Puedes consultar la agenda, pero solo se permiten citas desde el 23 de noviembre de 2026.'
                    : 'Selecciona una fila libre para rellenar automáticamente los datos de la cita.';
                try {
                    if (agendaMode === 'week') {
                        await renderWeek(selectedDate);
                    } else {
                        await loadBookings(selectedDate, selectedDate);
                        renderDay(selectedDate, currentBookings);
                    }
                } catch (error) {
                    document.getElementById('agenda-hint').textContent = error.message;
                }
            }

            async function doctorChanged() {
                const doctor = chosenDoctor();
                const hasDoctor = Boolean(doctors.value);
                doctorCard.hidden = !hasDoctor;
                calendarSection.hidden = !hasDoctor;
                if (!hasDoctor) {
                    patientPanel.hidden = true;
                    return;
                }
                document.getElementById('doctor-name').textContent = doctor.dataset.name;
                document.getElementById('doctor-code').textContent = doctors.value;
                document.getElementById('doctor-specialty').textContent = doctor.dataset.specialty;
                document.getElementById('form-doctor').value = doctors.value;
                const date = oldDate || isoDate(new Date());
                visibleMonth = new Date(`${date}T12:00:00`);
                selectedDate = '';
                try {
                    const start = isoDate(new Date(visibleMonth.getFullYear(), visibleMonth.getMonth(), 1, 12));
                    const end = isoDate(new Date(visibleMonth.getFullYear(), visibleMonth.getMonth() + 1, 0, 12));
                    await loadBookings(start, end);
                    if (oldDate && new Date(`${oldDate}T12:00:00`).getDay() % 6 !== 0) {
                        selectedDate = oldDate;
                        if (oldTime) chooseSlot(oldDate, oldTime);
                    } else if ({{ $cita ? 'true' : 'false' }}) {
                        selectedDate = oldDate;
                    }
                    renderCalendar();
                    if (selectedDate) await renderAgenda();
                } catch (error) {
                    document.getElementById('agenda-hint').textContent = error.message;
                }
            }

            document.getElementById('previous-month').addEventListener('click', async () => {
                visibleMonth.setMonth(visibleMonth.getMonth() - 1);
                await loadBookings(isoDate(new Date(visibleMonth.getFullYear(), visibleMonth.getMonth(), 1, 12)), isoDate(new Date(visibleMonth.getFullYear(), visibleMonth.getMonth() + 1, 0, 12)));
                renderCalendar();
            });
            document.getElementById('next-month').addEventListener('click', async () => {
                visibleMonth.setMonth(visibleMonth.getMonth() + 1);
                await loadBookings(isoDate(new Date(visibleMonth.getFullYear(), visibleMonth.getMonth(), 1, 12)), isoDate(new Date(visibleMonth.getFullYear(), visibleMonth.getMonth() + 1, 0, 12)));
                renderCalendar();
            });
            document.getElementById('day-view').addEventListener('click', async () => {
                agendaMode = 'day';
                document.getElementById('day-view').classList.add('view-active');
                document.getElementById('week-view').classList.remove('view-active');
                await renderAgenda();
            });
            document.getElementById('week-view').addEventListener('click', async () => {
                agendaMode = 'week';
                document.getElementById('week-view').classList.add('view-active');
                document.getElementById('day-view').classList.remove('view-active');
                await renderAgenda();
            });
            doctors.addEventListener('change', () => {
                patientPanel.hidden = true;
                selectedDate = '';
                selectedTime = '';
                doctorChanged();
            });
            document.getElementById('patient-select').addEventListener('change', (event) => {
                submitButton.disabled = !event.target.value || !selectedDate || !selectedTime;
            });
            doctorChanged();
        })();
    </script>
</body>
</html>
