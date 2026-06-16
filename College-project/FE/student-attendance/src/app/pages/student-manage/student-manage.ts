import { Component, signal } from '@angular/core';
import { ApiService } from '../../services/api.service';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { SearchPipe } from "../../pipes/search.pipe";


@Component({
  selector: 'app-student-manage',
  imports: [FormsModule, SearchPipe,ReactiveFormsModule],
  templateUrl: './student-manage.html',
  styleUrl: './student-manage.css',
})
export class StudentManage {
  students = signal<any[]>([]);
  departments = signal<any[]>([]);
  classes = signal<any[]>([]);
  searchText = '';
  filteredStudents = signal<any[]>([]);

  // simple form model
   roll_no = '';    
  student_name = '';
  dob = '';
  username = '';
  password = '';
  dept_id!: number;
  class_id!: number;
    editForm!: FormGroup;
  showEditModal = false;
  constructor(private api: ApiService,private fb:FormBuilder) { }

  ngOnInit() {
    this.loadMeta();
    this.loadStudents();
    this.initEditForm();
  }

  loadStudents() {
    this.api.fetchAllStudents().subscribe((res: any) => {
      this.students.set(res);
    });
  }

  loadMeta() {
    this.api.getMeta().subscribe((res: any) => {
      this.departments.set(res.departments);
      this.classes.set(res.classes);
    });
  }

  addStudent() {
    if (!this.student_name || !this.username) {
      alert('Fill required fields');
      return;
    }

    this.api.addStudent({
      Roll_no:this.roll_no,
      student_name: this.student_name,
      dob: this.dob,
      username: this.username,
      password: this.password,
      dept_id: this.dept_id,
      class_id: this.class_id
    }).subscribe(() => {
      alert('Student added');
      this.resetForm();
      this.loadStudents();
    });
  }

    initEditForm() {
    this.editForm = this.fb.group({
      student_id: [''],
      Roll_no: ['', Validators.required],
      student_name: ['', Validators.required],
      dob: ['', Validators.required],
      username: ['', Validators.required],
      password: ['', Validators.required],
      dept_id: ['', Validators.required],
      class_id: ['', Validators.required]
    });
  }
    // 🔹 Open Edit Popup
  openEditStudent(student: any) {
    this.showEditModal = true;
      const formattedDob = student.dob
    ? new Date(student.dob).toISOString().substring(0, 10)
    : '';
    this.editForm.patchValue({
      
      student_id: student.student_id,
      Roll_no: student.Roll_no,
      student_name: student.student_name,
      dob: formattedDob,
      username: student.username,
      password: student.password, 
      dept_id: student.dept_id,
      class_id: student.class_id
    });
  }

  // 🔹 Update Student
  updateStudent() {
    if (this.editForm.invalid) return;

    this.api.updateStudent(this.editForm.value).subscribe(() => {
      this.showEditModal = false;
      this.loadStudents();
    });
  }

  // 🔹 Close Popup
  closeModal() {
    this.showEditModal = false;
  }

  deleteStudent(student_id: number) {
    if (!confirm('Are you sure?')) return;

    this.api.deleteStudent({ student_id }).subscribe(() => {
      alert('Student deleted');
      this.loadStudents();
    });
  }

  resetForm() {
      this.roll_no = '';
    this.student_name = '';
    this.dob = '';
    this.username = '';
    this.password = '';
  }

}
