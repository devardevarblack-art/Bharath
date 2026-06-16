import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OverallAttendanceComponent } from './overall-attendance-component';

describe('OverallAttendanceComponent', () => {
  let component: OverallAttendanceComponent;
  let fixture: ComponentFixture<OverallAttendanceComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [OverallAttendanceComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(OverallAttendanceComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
