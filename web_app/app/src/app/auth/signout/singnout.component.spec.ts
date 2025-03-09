import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SingnoutComponent } from './singnout.component';

describe('SingnoutComponent', () => {
  let component: SingnoutComponent;
  let fixture: ComponentFixture<SingnoutComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SingnoutComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SingnoutComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
