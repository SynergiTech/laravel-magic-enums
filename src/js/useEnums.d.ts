import { enums } from '../../resources/js/magic-enums/magic-enums.js';

declare module 'useEnums.ts' {
  export function useEnums(): typeof enums;
}
