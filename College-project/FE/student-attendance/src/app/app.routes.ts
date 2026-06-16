import { Routes } from '@angular/router';
import { authGuard } from './guards/auth.guard';

export const routes: Routes = [
  { path: '', loadComponent: () => import('./pages/landing/landing').then((p)=> p.Landing) },
  { path: 'student-login', loadComponent: () => import('./pages/teacher-login/teacher-login').then((p)=> p.TeacherLogin) },
  { path: 'teacher-login', loadComponent: () => import('./pages/teacher-login/teacher-login').then((p)=> p.TeacherLogin) },
  { path: 'student-report', loadComponent: () => import('./pages/student-report/student-report').then((p)=> p.StudentReport) },
  { path: 'teacher/select', loadComponent: () => import('./pages/teacher-selection/teacher-selection').then((p)=> p.TeacherSelection), canActivate: [authGuard] },
  { path: 'teacher/attendance', loadComponent: () => import('./pages/attendance-entry/attendance-entry').then((p)=> p.AttendanceEntry), canActivate: [authGuard] },
  { path: 'teacher/students', loadComponent: () => import('./pages/student-manage/student-manage').then((p)=> p.StudentManage), canActivate: [authGuard] },
  { path: 'teacher-manage', loadComponent: () => import('./pages/teacher-manage/teacher-manage').then((p)=> p.TeacherManage), canActivate: [authGuard] },
  { path: 'overall', loadComponent: () => import('./pages/overall-attendance-component/overall-attendance-component').then((p)=> p.OverallAttendanceComponent), canActivate: [authGuard] },
];
