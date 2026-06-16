import { Component, signal } from '@angular/core';
import { ApiService } from '../../services/api.service';
import { AuthService } from '../../services/auth.service';
import { DatePipe } from '@angular/common';

@Component({
  selector: 'app-student-report',
  imports: [DatePipe],
  templateUrl: './student-report.html',
  styleUrl: './student-report.css',
})
export class StudentReport {
report = signal<any[]>([]);

  constructor(private api: ApiService, private auth: AuthService) {
    this.api.studentReport({
      student_id: auth.student()?.student_id,
      from_date: '2025-06-01',
      to_date: '2025-12-31'
    }).subscribe(res => this.report.set(res as any[]));
  }
}
