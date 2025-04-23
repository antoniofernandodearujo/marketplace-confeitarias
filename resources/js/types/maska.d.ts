declare module 'maska' {
  import { Directive } from 'vue'

  export const vMaska: Directive
  export function create(mask: string | string[]): (value: string) => string
  export function mask(value: string, mask: string | string[]): string
  export function tokens(mask: string, tokens?: Record<string, RegExp>): RegExp[]
}