import { ComponentFixture, TestBed } from '@angular/core/testing';

import { StudentManage } from './student-manage';

describe('StudentManage', () => {
  let component: StudentManage;
  let fixture: ComponentFixture<StudentManage>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [StudentManage]
    })
    .compileComponents();

    fixture = TestBed.createComponent(StudentManage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
