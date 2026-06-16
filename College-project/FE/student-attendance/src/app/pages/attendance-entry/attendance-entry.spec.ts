import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AttendanceEntry } from './attendance-entry';

describe('AttendanceEntry', () => {
  let component: AttendanceEntry;
  let fixture: ComponentFixture<AttendanceEntry>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AttendanceEntry]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AttendanceEntry);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
