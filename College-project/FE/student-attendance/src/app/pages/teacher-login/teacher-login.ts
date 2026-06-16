import { Component, OnInit, signal } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from '../../services/api.service';
import { AuthService } from '../../services/auth.service';
import { FormControl, FormGroup, FormsModule, Validators, ReactiveFormsModule } from '@angular/forms';
import { NgIf } from '@angular/common';

@Component({
  selector: 'app-teacher-login',
  imports: [FormsModule, ReactiveFormsModule, NgIf],
  templateUrl: './teacher-login.html',
  styleUrl: './teacher-login.css',
})
export class TeacherLogin implements OnInit {

  loginForm = new FormGroup({
    username: new FormControl('', Validators.required),
    password: new FormControl('', Validators.required),
  });
  loginError = '';

  isStudent = signal<boolean>(false);

  constructor(
    private api: ApiService,
    private auth: AuthService,
    private router: Router) { }

  ngOnInit() {
  this.isStudent.set(this.router.url.includes('student'));
  }

  login() {
    const payLoad = { username: this.loginForm.controls.username.value, password: this.loginForm.controls.password.value };
    const endPoint = this.isStudent() ?this.api.studentLogin(payLoad) :this.api.teacherLogin(payLoad) ;
    endPoint.subscribe(
      {
        next: res => this.navigate(res),
        error: (err) => {
          if (err.status === 404) {
            this.loginError = 'Invalid username or password';
          } else {
            this.loginError = 'Something went wrong. Please try again.';
          }
        }
      }
    );
  }

  navigate(res: any) {
    if (this.isStudent()) {
      this.auth.loginStudent(res);
      this.router.navigate(['/student-report']);
    } else {
      this.auth.loginTeacher(res);
      this.router.navigate(['/teacher/select']);
    }
  }
}
