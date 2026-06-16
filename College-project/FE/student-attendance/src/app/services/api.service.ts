import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({ providedIn: 'root' })
export class ApiService {
  baseUrl = 'http://localhost:3000/api';

  constructor(private http: HttpClient) { }

  teacherLogin(payload: any) {
    return this.http.post(`${this.baseUrl}/teacher/login`, payload);
  }

  studentLogin(payload: any) {
    return this.http.post(`${this.baseUrl}/student/login`, payload);
  }

  getMeta() {
    return this.http.post(`${this.baseUrl}/meta/departments-classes`, {});
  }

  fetchStudentsForAttendance(payload: any) {
    return this.http.post(`${this.baseUrl}/attendance/students`, payload);
  }

  fetchAllStudents() {
    return this.http.get(`${this.baseUrl}/student/all`);
  }

  addAttendance(payload: any) {
    return this.http.post(`${this.baseUrl}/attendance/add`, payload);
  }

  studentReport(payload: any) {
    return this.http.post(`${this.baseUrl}/student/report`, payload);
  }

  addStudent(payload: any) {
    return this.http.post(`${this.baseUrl}/student/add`, payload);
  }

  deleteStudent(payload: any) {
    return this.http.post(`${this.baseUrl}/student/delete`, payload);
  }

  updateStudent(payload: any) {
    return this.http.post(`${this.baseUrl}/student/update`, payload);
  }

  updateTeacher(payload: any) {
    return this.http.post(`${this.baseUrl}/teacher/update`, payload);
  }

  getAttendanceReport(payload: any) {
    return this.http.post(`${this.baseUrl}/report/attendance/department-wise`, payload);
  }

}
