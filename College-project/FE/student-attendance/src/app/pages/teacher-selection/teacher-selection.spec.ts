import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TeacherSelection } from './teacher-selection';

describe('TeacherSelection', () => {
  let component: TeacherSelection;
  let fixture: ComponentFixture<TeacherSelection>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [TeacherSelection]
    })
    .compileComponents();

    fixture = TestBed.createComponent(TeacherSelection);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
