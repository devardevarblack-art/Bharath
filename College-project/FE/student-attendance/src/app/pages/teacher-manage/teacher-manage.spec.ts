import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TeacherManage } from './teacher-manage';

describe('TeacherManage', () => {
  let component: TeacherManage;
  let fixture: ComponentFixture<TeacherManage>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [TeacherManage]
    })
    .compileComponents();

    fixture = TestBed.createComponent(TeacherManage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
