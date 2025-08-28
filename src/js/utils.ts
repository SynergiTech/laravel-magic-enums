import { execSync } from 'node:child_process';
import path from 'node:path';

export function artisan(command: string): void {
  const testbenchDir = path.join('vendor', 'bin', 'testbench');
  execSync(`${testbenchDir} ${command}`).toString('utf8');
}
