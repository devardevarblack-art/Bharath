import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class TeacherService {
  private baseUrl = 'http://localhost:3000/api/teacher';

  constructor(private http: HttpClient) {}

  // Fetch all teachers
  fetchAllTeachers(): Observable<any[]> {
    return this.http.get<any[]>(`${this.baseUrl}/all`);
  }

  // Add new teacher
  addTeacher(teacherData: any): Observable<any> {
    return this.http.post<any>(`${this.baseUrl}/add`, teacherData);
  }

  // Delete a teacher
  deleteTeacher(payload: { teacher_id: number }): Observable<any> {
    return this.http.post<any>(`${this.baseUrl}/remove`, payload);
  }

}
