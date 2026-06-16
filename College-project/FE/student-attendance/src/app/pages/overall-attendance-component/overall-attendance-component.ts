import { Component, OnInit, signal } from '@angular/core';
import { ApiService } from '../../services/api.service';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-overall-attendance',
  templateUrl: './overall-attendance-component.html',
  imports:[FormsModule,CommonModule]
})
export class OverallAttendanceComponent implements OnInit {

  rawData: any[] = [];
  summaryList= signal<any[]>([])

  fromDate!: string;
  toDate!: string;

  constructor(private api: ApiService) {}

  ngOnInit() {
    // optional default dates
    this.fromDate = '2025-12-01';
    this.toDate = '2025-12-31';

    this.fetchAttendanceReport();
  }

  fetchAttendanceReport() {
    const payload = {
      from_date: this.fromDate,
      to_date: this.toDate
    };

    this.api.getAttendanceReport(payload).subscribe((res: any) => {
      this.rawData = res;
      this.prepareSummary(res);
    });
  }

  prepareSummary(data: any[]) {
    const result: any[] = [];

    data.forEach(dept => {
      dept.classes.forEach((cls: any) => {
        cls.students.forEach((student: any) => {

          const totalHours = student.attendance.length;
          const presentHours = student.attendance.filter(
            (a: any) => a.status === 'P'
          ).length;

          result.push({
            student_id: student.student_id,
            student_name: student.student_name,
            dept_name: dept.dept_name,
            class_name: cls.class_name,
            total_hours: totalHours,
            present_hours: presentHours,
            attendance_percentage: totalHours
              ? Math.round((presentHours / totalHours) * 100)
              : 0
          });
        });
      });
    });

    this.summaryList.set(result);
  }
}
