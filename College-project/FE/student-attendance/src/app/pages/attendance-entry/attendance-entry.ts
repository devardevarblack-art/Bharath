import { Component, signal } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from '../../services/api.service';
import { AuthService } from '../../services/auth.service';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-attendance-entry',
  imports: [FormsModule],
  templateUrl: './attendance-entry.html',
  styleUrl: './attendance-entry.css',
})
export class AttendanceEntry {
  students = signal<any[]>([]);

  dept_id!: number;
  class_id!: number;
  hour_id!: number;
  attendance_date!: string;

  constructor(
    private router: Router,
    private api: ApiService,
    private auth: AuthService
  ) {
    const nav = history.state;

    this.dept_id = nav.dept_id;
    this.class_id = nav.class_id;
    this.hour_id = nav.hour_id;
    this.attendance_date = nav.attendance_date;

    if (!this.dept_id) {
      this.router.navigate(['/teacher/select']);
      return;
    }

    this.loadStudents();
  }

  loadStudents() {
    this.api.fetchStudentsForAttendance({
      dept_id: this.dept_id,
      class_id: this.class_id,
      hour_id: this.hour_id,
      attendance_date: this.attendance_date
    }).subscribe((res: any) => {
      // Convert status → checkbox boolean
      this.students.set(
        res.map((s:any) => ({
          ...s,
          present: s.status === 'P'
        }))
      );
    });
  }

  submitAttendance() {
    const payload = {
      teacher_id: this.auth.teacher()?.teacher_id,
      dept_id: this.dept_id,
      class_id: this.class_id,
      hour_id: this.hour_id,
      attendance_date: this.attendance_date,
      attendance: this.students().map(s => ({
        student_id: s.student_id,
        status: s.present ? 'P' : 'A'
      }))
    };

    this.api.addAttendance(payload).subscribe(() => {
      alert('Attendance submitted successfully');
      this.router.navigate(['/teacher/select']);
    });
  }

  markAllAsPresent(){
    const data = this.students();
    data.map((st:any)=> st.present = true);
    this.students.set(data);
  }
}
