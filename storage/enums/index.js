export const enums = {
  EnumWithSpaces: {
    TwentyOne: { name: 'TwentyOne', value: 'Twenty One' },
    TwentyTwo: { name: 'TwentyTwo', value: 'Twenty Two' },
  },
  CustomEnum: {
    Alpha: {
      name: 'Alpha',
      value: 'alpha',
      add_three: 'delta',
      'something else': 'alpha',
    },
    Beta: {
      name: 'Beta',
      value: 'beta',
      add_three: 'echo',
      'something else': 'beta',
    },
    Charlie: {
      name: 'Charlie',
      value: 'charlie',
      add_three: 'foxtrot',
      'something else': 'charlie',
    },
  },
  TestingEnum: {
    First: { name: 'First', value: 'first', colours: 'purple' },
    Second: { name: 'Second', value: 'second', colours: 'yellow' },
    Third: { name: 'Third', value: 'third', colours: 'green' },
    Fourth: { name: 'Fourth', value: 'fourth', colours: null },
    Fifth: { name: 'Fifth', value: 'fifth', colours: null },
    Sixth: { name: 'Sixth', value: 'sixth', colours: null },
    Seventh: { name: 'Seventh', value: 'seventh', colours: null },
    Eighth: { name: 'Eighth', value: 'eighth', colours: null },
  },
  TestingEnumThreeQuarters: {
    First: { name: 'First', value: 'first', colours: 'purple' },
    Second: { name: 'Second', value: 'second', colours: 'yellow' },
    Third: { name: 'Third', value: 'third', colours: 'green' },
    Fourth: { name: 'Fourth', value: 'fourth', colours: null },
    Fifth: { name: 'Fifth', value: 'fifth', colours: null },
    Sixth: { name: 'Sixth', value: 'sixth', colours: null },
  },
};
for (const key in enums) {
  enums[key] = new Proxy(enums[key], {
    get(target, prop) {
      if (typeof prop !== 'string') {
        return false;
      }

      const normalisedKey = prop.replaceAll(' ', '');

      if (Reflect.has(target, normalisedKey)) {
        return Reflect.get(target, normalisedKey);
      }

      return false;
    },
  });
}
Object.freeze(enums);
