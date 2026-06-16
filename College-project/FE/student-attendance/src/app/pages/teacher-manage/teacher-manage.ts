import { Component, signal } from '@angular/core';
import { TeacherService } from '../../services/teacher.service';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { ApiService } from '../../services/api.service';
import { SearchPipe } from "../../pipes/search.pipe";

@Component({
  selector: 'app-teacher-manage',
  imports: [FormsModule, SearchPipe, ReactiveFormsModule],
  templateUrl: './teacher-manage.html',
  styleUrl: './teacher-manage.css',
})
export class TeacherManage {
  teachers = signal<any[]>([]);
  departments = signal<any[]>([]);

  // Form model
  teacher_name = '';
  username = '';
  password = '';
  years_of_experience = 0;
  specialised_subject = '';
  dept_id!: number;
  searchText = '';
  showEditTeacherModal = false;
  editTeacherForm!: FormGroup;
  constructor(private api: TeacherService, private api1: ApiService, private fb: FormBuilder) {
    this.loadMeta();
    this.loadTeachers();
  }
  ngOnInit() {
    this.editTeacherForm = this.fb.group({
      teacher_id: [''],
      teacher_name: ['', Validators.required],
      years_of_experience: ['', Validators.required],
      specialised_subject: ['', Validators.required],
      username: ['', Validators.required],
      password: ['', Validators.required],
      dept_id: ['', Validators.required]
    });
  }

  // Load all teachers
  loadTeachers() {
    this.api.fetchAllTeachers().subscribe((res: any[]) => {
      this.teachers.set(res);
    });
  }

  // Load all departments
  loadMeta() {
    this.api1.getMeta().subscribe((res: any) => {
      this.departments.set(res.departments);
    });
  }

  addTeacher() {
    if (!this.teacher_name || !this.username || !this.password || !this.dept_id) {
      alert('Please fill all required fields');
      return;
    }

    this.api.addTeacher({
      teacher_name: this.teacher_name,
      username: this.username,
      password: this.password,
      years_of_experience: this.years_of_experience,
      specialised_subject: this.specialised_subject,
      dept_id: this.dept_id
    }).subscribe(() => {
      alert('Teacher added successfully');
      this.resetForm();
      this.loadTeachers();
    });
  }
  openEditTeacher(teacher: any) {
    this.showEditTeacherModal = true;
    const dept_id = this.departments().find((dept: any) => dept.dept_name == teacher.dept_name).dept_id;
    this.editTeacherForm.patchValue({
      teacher_id: teacher.teacher_id,
      teacher_name: teacher.teacher_name,
      years_of_experience: teacher.years_of_experience,
      specialised_subject: teacher.specialised_subject,
      username: teacher.username,
      password: teacher.password,
      dept_id: dept_id
    });
  }

  updateTeacher() {
    if (this.editTeacherForm.invalid) return;

    this.api1.updateTeacher(this.editTeacherForm.value)
      .subscribe(() => {
        alert('Teacher Details updated successfully');
        this.closeTeacherModal();
        this.loadTeachers();
      });
  }
  closeTeacherModal() {
    this.showEditTeacherModal = false;
    this.editTeacherForm.reset();
  }

  deleteTeacher(teacher_id: number) {
    if (!confirm('Are you sure you want to delete this teacher?')) return;

    this.api.deleteTeacher({ teacher_id }).subscribe(() => {
      alert('Teacher deleted successfully');
      this.loadTeachers();
    });
  }

  resetForm() {
    this.teacher_name = '';
    this.username = '';
    this.password = '';
    this.years_of_experience = 0;
    this.specialised_subject = '';
    this.dept_id = 0;
  }
}
