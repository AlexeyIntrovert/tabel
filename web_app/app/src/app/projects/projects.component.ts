import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatDialogModule, MatDialog } from '@angular/material/dialog';
import { ProjectService } from '../core/project/project.service';
import { Project } from '../core/project/project.interface';
import { ProjectCreateComponent } from './create/create.component';

@Component({
  selector: 'app-projects',
  standalone: true,
  imports: [
    CommonModule,
    MatTableModule,
    MatButtonModule,
    MatIconModule,
    MatDialogModule
  ],
  templateUrl: './projects.component.html',
  styleUrls: ['./projects.component.css']
})
export class ProjectsComponent implements OnInit {
  projects: Project[] = [];
  displayedColumns: string[] = ['name', 'actions'];

  constructor(
    private projectService: ProjectService,
    private dialog: MatDialog
  ) {}

  ngOnInit(): void {
    this.loadProjects();
  }

  loadProjects(): void {
    this.projectService.getProjects().subscribe(projects => {
      this.projects = projects;
    });
  }

  openDialog(project?: Project): void {
    const dialogRef = this.dialog.open(ProjectCreateComponent, {
      width: '400px',
      data: project || { name: '' }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        if (project) {
          this.updateProject(project.uid, result.name);
        } else {
          this.createProject(result.name);
        }
      }
    });
  }

  createProject(name: string): void {
    this.projectService.createProject(name).subscribe(() => {
      this.loadProjects();
    });
  }

  updateProject(uid: string, name: string): void {
    this.projectService.updateProject(uid, name).subscribe(() => {
      this.loadProjects();
    });
  }

  deleteProject(uid: string): void {
    if (confirm('Are you sure you want to delete this project?')) {
      this.projectService.deleteProject(uid).subscribe(() => {
        this.loadProjects();
      });
    }
  }
}
