import { Injectable, signal } from '@angular/core';

@Injectable({ providedIn: 'root' })
export class AuthService {
  teacher = signal<any>(null);
  student = signal<any>(null);

  loginTeacher(data: any) {
    this.teacher.set(data);
  }

  loginStudent(data: any) {
    this.student.set(data);
  }

  logout() {
    this.teacher.set(null);
    this.student.set(null);
  }

  isTeacherLoggedIn() {
    return !!this.teacher();
  }
}
