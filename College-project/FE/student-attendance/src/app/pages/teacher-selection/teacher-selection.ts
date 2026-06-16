import { Component, signal } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { ApiService } from '../../services/api.service';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-teacher-selection',
  imports: [FormsModule, RouterLink],
  templateUrl: './teacher-selection.html',
  styleUrl: './teacher-selection.css',
})
export class TeacherSelection {
  departments = signal<any[]>([]);
  classes = signal<any[]>([]);

  selectedDept = signal<number | null>(null);
  selectedClass = signal<number | null>(null);
  selectedHour = signal<number | null>(1);
  selectedDate = signal<string>(
    new Date().toISOString().split('T')[0]
  );

  constructor(private api: ApiService, private router: Router) {
    this.loadMeta();
  }

  loadMeta() {
    this.api.getMeta().subscribe((res: any) => {
      this.departments.set(res.departments);
      this.classes.set(res.classes);
      this.selectedDept.set(res.departments[0].dept_id);
      this.selectedClass.set(res.classes[0].class_id);
    });
  }

  proceed() {
    if (!this.selectedDept() || !this.selectedClass() || !this.selectedHour()) {
      alert('Please select all fields');
      return;
    }

    this.router.navigate(['/teacher/attendance'], {
      state: {
        dept_id: this.selectedDept(),
        class_id: this.selectedClass(),
        hour_id: this.selectedHour(),
        attendance_date: this.selectedDate()
      }
    });
  }
}
