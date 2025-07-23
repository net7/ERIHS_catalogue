import colors from 'tailwindcss/colors'
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'false',
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './vendor/laravel/jetstream/**/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/**/*.blade.php',
    './app/Filament/**/*.php',
    './app/Livewire/**/*.php',
    './app/Livewire/*.php',
    './resources/views/filament/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
  ],

  theme: {
    extend: {
      backgroundImage: {
        'erihs-bg': "url('../../public/images/erihsbg.svg')",
        'catalogue-bg': "url('../../public/images/grid-circle.png')",
      },

      fontFamily: {
        // sans: ['Figtree', ...defaultTheme.fontFamily.sans],
        sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        danger: {

          '50': '#fff1f2',
          '100': '#FDD9DF',
          '200': '#FBB2BF',
          '300': '#F88C9E',
          '400': '#F88C9E',
          '500': '#F43F5E',
          '600': '#E11C48',
          '700': '#B4173A',
          '800': '#621926',
          '900': '#310D13',
          '950': '#310D13'
        },
        primary: {

          '50': '#D7EEF0',
          '100': '#C3E6E9',
          '200': '#B0DEE2',
          '300': '#9CD5DA',
          '400': '#44676A',
          '500': '#395658',
          '600': '#2D4547',
          '700': '#223435',
          '800': '#172223',
          '900': '#0E1515',
          '950': '#0E1515',
        },
        success: {
          '50': '#F2FDF5',
          '100': '#D3F3DF',
          '200': '#A7E8BF',
          '300': '#7ADC9E',
          '400': '#4ED17E',
          '500': '#22C55E',
          '600': '#16A34A',
          '700': '#147638',
          '800': '#0E4F26',
          '900': '#072713',
          '950': '#072713',
        },
        warning: {
          '50': '#FFFBEB',
          '100': '#FDECCE',
          '200': '#FBD89D',
          '300': '#F9C56D',
          '400': '#F7B13C',
          '500': '#F59E0B',
          '600': '#D97706',
          '700': '#A96E09',
          '800': '#623F04',
          '900': '#312002',
          '950': '#312002',
        },
        custom: {

          '50': '#D7EEF0',
          '100': '#C3E6E9',
          '200': '#B0DEE2',
          '300': '#9CD5DA',
          '400': '#44676A',
          '500': '#395658',
          '600': '#2D4547',
          '700': '#223435',
          '800': '#172223',
          '900': '#0E1515',
          '950': '#0E1515',
        }
      },
    },
  },

  plugins: [forms, typography],
};
